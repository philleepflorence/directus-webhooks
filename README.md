# Directus WebHooks

Directus WebHooks requires the collection app_webhooks to be installed!
You may turn off or on webhooks from within the Directus Settings Collection.
This module allows the API to send out requests to any URL when one of the existing events are triggered during the Directus LifeCycle.

Avoid using webhooks when making requests to the API from an external application as that would increase the response time.
Create internal hooks within your application!

This module is useful for edits made within the Directus App.
