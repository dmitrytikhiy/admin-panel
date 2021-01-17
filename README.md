cp .env.example-workspace .env
docker build --build-arg APP_UID=`id -u` -t admin-panel:devel -f Dockerfile.devel .

docker run -d \
    --network workspace \
    --name admin-panel.workspace \
    -v $(pwd):/app:delegated \
    -e XDEBUG_CONFIG="remote_host=172.17.0.1 remote_connect_back=0" \
    admin-panel:devel
