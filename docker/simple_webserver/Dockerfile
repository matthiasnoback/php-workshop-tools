FROM matthiasnoback/php_workshop_base

# Expose a running instance of PHP's built-in web server
EXPOSE 80
ENTRYPOINT ["php", "-S", "0.0.0.0:80", "-t"]

# Set the document root (can be overridden by providing a custom "command" when running this container)
CMD ["web/"]
