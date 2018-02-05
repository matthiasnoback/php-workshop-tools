FROM alpine:3.7
RUN apk add --no-cache bash curl
COPY run_tests.sh /usr/bin
VOLUME /opt
WORKDIR /opt
ENTRYPOINT ["run_tests.sh"]
