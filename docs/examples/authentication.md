# Authentication
* The package was designed to use Google IAM Authentication. This requires using the following composer.json file:
```
{
  "require": {
    "thpglobal/classes": "dev-main",
    "php": ">=7.1",
    "google/auth": "^1.9",
    "google/cloud-core": "^1.32",
    "kelvinmo/simplejwt": "dev-master",
    "google/cloud-storage": "^1.16"
  }
}
```
* In your Google App Engline application you'll need to enable it to use the Identity-Aware-Proxy described at [https://cloud.google.com/iap/docs/concepts-overview]thie lins.:
