FROM ubuntu:precise
RUN apt-get update -y && apt-get install -y curl
COPY run_tests.sh /usr/bin
ENTRYPOINT ["run_tests.sh"]
