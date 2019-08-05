# Directus WebHooks - v6.X

Directus WebHooks requires the collection app_webhooks to be installed!
You may turn off or on webhooks from within the Directus Settings Collection - `directus_settings -> webhooks.active = 1`.
This module allows the API to send out requests to any URL when one of the existing events are triggered during the Directus LifeCycle.

Avoid using webhooks when making requests to the API from an external application as that would increase the response time.
Create internal hooks within your application!

This module is useful for edits made within the Directus App.

## Life Cycle

1. `api/api.php` - Instantiate Webhooks for API Calls only.
2. `Directus\Bootstrap` - Bootstrap Instantiator - to avoid creating multiple instances.
3. `Directus\Hook\Emitter` - Call the Webhook Emitter Run Method when Hook Emitter Run Method is called.
4. `Directus\Webhooks\Emitter` - Cache all events that match rows in **app_webhooks** and then run on **__destruct()**
