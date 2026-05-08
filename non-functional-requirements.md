@startuml
package "Frontend" {
  class LoginPage
  class HTTP Client (axios)
}

package "API Layer" {
  class Routes (api.php)
  class AuthMiddleware
  class AuthController
}

package "Models" {
  class User
}

package "Infrastructure" {
  class Database
}

' ===== Frontend =====
LoginPage --> HTTP Client (axios) : send login request

' ===== API Layer =====
HTTP Client (axios) --> Routes (api.php)
Routes (api.php) --> AuthController
AuthController --> AuthMiddleware

' ===== Business (in Controller) =====
AuthController --> User : find user / verify password

' ===== Data Access =====
User --> Database
@enduml