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
            entity:
                class: SSO\FpBundle\Entity\User
                property: email
    firewalls:
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticators:
                - SSO\FpBundle\Security\FactoryPortalAuthenticator
                - SSO\FpBundle\Security\SSO\UserExperienceAuthenticator
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

### Configure env
```Env
OAUTH_FACTORY_PORTAL_ID='<factory_portal_id>'
OAUTH_FACTORY_PORTAL_SECRET='<factory_portal_secret>'
```
    