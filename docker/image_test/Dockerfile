FROM ubuntu:16.10
RUN apt-get update -y && apt-get install -y curl
COPY run_tests.sh /usr/bin
VOLUME /opt
WORKDIR /opt
ENTRYPOINT ["run_tests.sh"]
