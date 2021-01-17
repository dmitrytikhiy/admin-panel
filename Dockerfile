FROM node:12 AS builder-node
COPY ./resources/ /usr/src/app/resources/
COPY ./package.json /usr/src/app/package.json
COPY ./package-lock.json /usr/src/app/package-lock.json
COPY ./webpack.mix.js /usr/src/app/webpack.mix.js
COPY ./.babelrc /usr/src/app/.babelrc
RUN cd /usr/src/app && \
    npm install && \
    npm run prod

FROM composer AS builder-composer
COPY ./composer.json /usr/src/app/composer.json
COPY ./composer.lock /usr/src/app/composer.lock

# Downloads packages in parallel to speed up the installation process
RUN composer global require hirak/prestissimo

RUN cd /usr/src/app && \
    composer install --prefer-dist --no-scripts --no-dev --no-autoloader --ignore-platform-reqs
COPY ./ /usr/src/app/
RUN cd /usr/src/app && \
    composer dump-autoload --optimize

# Runtime setup

FROM webdevops/php-nginx:7.3

EXPOSE 80
ENV PORT=80

ARG APP_UID=1000
ENV APP_UID=${APP_UID}

RUN if ! [ $APP_UID = "1000" ]; then\
    userdel application && \
    groupadd --gid $APP_UID application && \
    useradd --uid $APP_UID --gid $APP_UID --shell /bin/bash --create-home application && \
    chown -R application:application /home/application; \
fi

ENV PHP_MEMORY_LIMIT=256M
ENV PHP_UPLOAD_MAX_FILESIZE=256M
ENV PHP_POST_MAX_SIZE=256M
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

RUN rm \
    /opt/docker/etc/supervisor.d/ssh.conf \
    /opt/docker/etc/supervisor.d/postfix.conf

RUN echo 'client_max_body_size 512M;' > /opt/docker/etc/nginx/vhost.common.d/10-general.conf

RUN su application -c "echo '* * * * * . /home/application/env.sh; cd /app && /usr/local/bin/php /app/artisan schedule:run >>/dev/null 2>&1' | crontab -"

# Production setup part

COPY --from=builder-composer /usr/src/app/ /app
COPY --from=builder-node /usr/src/app/public/js/ /app/public/js/
COPY --from=builder-node /usr/src/app/public/css/ /app/public/css/
#COPY --from=builder-node /usr/src/app/public/images/ /app/public/images/
#COPY --from=builder-node /usr/src/app/public/mix-manifest.json /app/public/mix-manifest.json

RUN chown -R application:application /app/storage

RUN rm -f /app/public/storage 2>/dev/null
RUN php artisan storage:link

VOLUME [ "/app/storage/app" ]

CMD su application -c "printenv | sed 's/^\([^=]*\)=\(.*\)/\1=\"\2\"/g' | sed 's/^\(.*\)\$/export \1/g' > /home/application/env.sh" \
    && supervisord
    