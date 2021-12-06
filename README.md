# Website Dashboard Connector Module

Provides functionality to integrate with website dashboard portal.
It extracts all the useful information from your website and exposes
all of it via APIs. The APIs are then consumed by the application 
written on the portal.

## API Reference

All the APIs use Oauth2 Authentication. 

#### Get status report information.

```http
  GET /website-information/status-report
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `oauth_token` | `string` | **Required**. The Oauth oauth_token saved for the website. |

## Authors

- [@gkapoor121212](https://github.com/gkapoor121212)
