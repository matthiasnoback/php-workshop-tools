FROM ubuntu:16.10
ENV DOCKER_COMPOSE_VERSION=1.11.1
RUN apt-get update -y && \
    apt-get install -y --no-install-recommends \
        apt-transport-https \
        ca-certificates \
        curl \
        software-properties-common && \
    curl -fsSL https://apt.dockerproject.org/gpg | apt-key add - && \
    add-apt-repository \
           "deb https://apt.dockerproject.org/repo/ \
           ubuntu-$(lsb_release -cs) \
           main" && \
   apt-get update -y && \
   apt-get -y install docker-engine
RUN curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m` > docker-compose && \
    chmod +x docker-compose && \
    mv docker-compose /usr/local/bin
RUN docker -v
RUN docker-compose -v
WORKDIR /opt
VOLUME /opt
