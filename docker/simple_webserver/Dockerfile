FROM matthiasnoback/php_workshop_tools_base

# Expose a running instance of PHP's built-in web server
EXPOSE 8080
ENTRYPOINT ["php", "-S", "0.0.0.0:8080", "-t"]

# The built-in PHP webserver only responds to SIGINT, not to SIGTERM
STOPSIGNAL SIGINT

# Set the document root (can be overridden by providing a custom "command" when running this container)
CMD ["web/"]
