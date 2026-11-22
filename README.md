# belvg/sso-fpbundle

Factory Portal SSO for Symfony apps: login through Factory Portal (OAuth2), user data and roles from `/api/v3/user`.

| Bundle | Symfony     | PHP  | Doctrine                                      |
|--------|-------------|------|-----------------------------------------------|
| 8.x    | 7.2 – 8.x   | 8.2+ | ORM 3, DoctrineBundle 2.7+ or 3, Migrations 3.2+ or 4 |
| 7.2    | 7.2 – 7.4   | 8.2+ | ORM 3, DoctrineBundle 2.7+                    |

## 8.0.0

- Symfony 8: routes use `Symfony\Component\Routing\Attribute\Route` (the `Annotation` namespace is gone in 8),
  services are configured in PHP (`src/config/services.php`) because Symfony 8 can not load XML service files.
- Works with DoctrineBundle 3 and DoctrineMigrationsBundle 4.
- Blocked users: the bundle reads `blockedUser`, the key Factory Portal sends. It used to read `blockeduser`,
  so a user blocked in Factory Portal was never marked as blocked in the app.
- Removed the `SSO\FpBundle\Security\SSO\UserExperienceAuthenticator` service: its class did not exist.

Upgrading from 7.x: `composer require belvg/sso-fpbundle:^8.0`. The app configuration below stays the same.

# Configuration
### Configure routes
#### Copy file `src/config/sso_fp.yaml` to `config/routes`

### Configure knp_client bundle
#### copy file `src/config/knpu_oauth2_client.yaml` to `config/packages/knpu_oauth2_client.yaml`

### Security
#### add to `src/config/security.yaml` add next lines:
```yaml
    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            id: SSO\FpBundle\Provider\FactoryPortalUserProvider 
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticators:
                - SSO\FpBundle\Security\FactoryPortalAuthenticator
            logout:
                path: app_logout
                target: factory_portal_logout
# add to access_controll section
    access_control:
      # - { path: ^/admin, roles: ROLE_ADMIN }
      # - { path: ^/profile, roles: ROLE_USER }
      - { path: ^/connect/factoryportal, roles: PUBLIC_ACCESS }
      - { path: ^/fp_logout, roles: PUBLIC_ACCESS }
      - { path: ^/connect/factoryportal/check, roles: PUBLIC_ACCESS }
      - { path: ^/, roles: IS_AUTHENTICATED_FULLY }
```

### Services
#### add to `config/service.yaml` add next lines:
````yaml
    SSO\FpBundle\Provider\FactoryOauth2ClientProvider:
        arguments:
            $options: { clientId: '%env(OAUTH_FACTORY_PORTAL_ID)%', clientSecret: '%env(OAUTH_FACTORY_PORTAL_SECRET)%' }
        public: true
#
    app.factory.provider:
        alias: SSO\FpBundle\Provider\FactoryOauth2ClientProvider
#
#
#
    KnpU\OAuth2ClientBundle\Client\OAuth2Client:
        arguments:
            $provider: '@app.factory.provider'

````
### Copy file `scr/config/sso_fp.yaml` to `config/routes`

### Configure env
```Env
OAUTH_FACTORY_PORTAL_ID='<factory_portal_id>'
OAUTH_FACTORY_PORTAL_SECRET='<factory_portal_secret>'
```
    