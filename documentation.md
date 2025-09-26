### Models

#### Users
| name | types | descriptions |
| :--- | :--- | :--- |
| id | serial | PK |
| name | varchar(255) | - |
| email | varchar(255) | Unique |
| password | char(32) | Hash : SHA(256) + salt |

#### /login
##### parameters
- email
- password

#### /logout
##### parameters
