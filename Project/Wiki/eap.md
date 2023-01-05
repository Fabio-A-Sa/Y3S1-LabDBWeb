# EAP: Architecture Specification and Prototype

## A7: Web Resources Specification

This artifact describes the web api that will be developed, remarking the resources needed, Its properties and JSON responses. This api includes creating, reading, updating and deleting operations (if it exists) for each resource.

### 1. Overview

| Modules | Description |
|---------|-------------|
| M01: Authentication | Web resources associated with user authentication and registration, with features such as login/logout and registration. |
| M02: Users | Web resources related to viewing user information, such as home page, profile page and edit profile information, chatting with other users and manipulating their own notifications. |
| M03: Posts | Web resources associated with posts, with features such as post creation, editing, deletion and visualization. |
| M04: Search | Web resources correspond to search features. Including searching users, groups, posts and comments with different types of privilege. |
| M05: Administration | Web resources associated with enforcing terms of service, blocking, unblocking and banning users, deleting posts/comments and updating static pages. |
| M06: Comments | Web resources associated with comments, with features such as comment creation, editing, deletion and visualization. |
| M07: Groups | Web resources associated with groups, like group creation, edition and deletion and interactions with groups. |

Table 62: OnlyFEUP resources overview

### 2. Permissions

This segment describes the permissions used in the last section (modules) to settle the conditions of access to resources.

| Identifier | Name | Description |
|------------|------|-------------|
| VST | Visitor | An unauthenticated user. |
| USR | User | An authenticated user. |
| OWN | Owner | The owner of a post, comment, profile. |
| ADM | Administrator | Platform administrator. |

Table 63: OnlyFEUP permissions

### 3. OpenAPI Specification

This section includes the [OnlyFEUP OpenAPI Specification](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/a7_openapi.yaml).

```yaml
openapi: 3.0.0

info:
 version: '2.0'
 title: 'OnlyFEUP Web API'
 description: 'Web Resources Specification (A9) for OnlyFEUP'

servers:
- url: https://lbaw2255.lbaw.fe.up.pt
  description: Production server

externalDocs:
 description: Find more info here.
 url: https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/wikis/eap

tags:
 - name: 'M01: Authentication'
 - name: 'M02: Users'
 - name: 'M03: Posts'
 - name: 'M04: Search'
 - name: 'M05: Administration'
 - name: 'M06: Comments'
 - name: 'M07: Groups'
 - name: 'M08: API'

paths:

############################################ AUTENTICATION ############################################

######### LOGIN #########

  /login:

    get:
      operationId: R101
      summary: 'R101: Login Form'
      description: 'Provide login form. Access: VST'
      tags:
        - 'M01: Authentication'

      responses:
        '200':
          description: 'OK. Show log-in UI'

    post:
      operationId: R102
      summary: 'R102: Login Action'
      description: 'Processes the login form submission. Access: VST'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urllencoded:
            schema:
              properties:
                email:
                  type: string
                  format: email
                password:
                  type: string
                  format: password
              required:
                  - email
                  - password

      responses:
        '302':
          description: 'Redirect after processing the new user information.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'OK. You are in. Redirect to homepage.'
                  value: '/home'
                302Failure:
                  description: 'You shall not pass. Redirect again to login form.'
                  value: '/login'

######### LOGOUT #########

  /logout:

    get:
      operationId: R103
      summary: 'R103 : Logout Action'
      description: 'Logout the current logged user. Access: USR, ADM'
      tags:
        - 'M01: Authentication'

      responses:
        '302':
          description: 'Redirect after processing logout.'
          headers:
            Location:
              schema:
                type: string
              examples:
                302Success:
                  description: 'Successful logout. Redirect to public feed.'
                  value: '/'

######### REGISTER #########

  /register:

    get:
      operationId: R104
      summary: 'R104 : Register Form'
      description: 'Register a new user. Access: VST'
      tags:
        - 'M01: Authentication'

      responses:
        '200':
          description: 'Ok. Lets Sign-up.'

    post:
      operationId: R105
      summary: 'R105 : Register Action'
      description: 'Processes the new user registration form submission. Access: VST'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                username:
                  type: string
                name:
                  type: string
                email:
                  type: string
                  format: email
                password:
                  type: string
                  format: password
                confirm_password:
                  type: string
                  format: password
              required:
                - username
                - name
                - email
                - password
                - confirm_password;

      responses:
        '302':
          description: 'Redirect after processing the new user information.'
          headers:
            Location:
              schema:
                type: string
              example:
                  302Success:
                    description: 'Successful registration. Redirect to home page.'
                    value: '/home'
                  302Failure:
                    description: 'Failed registration. Redirect again to register form.'
                    value: '/register'

######### SEND EMAIL #########

  /sendEmail:

    post:
      operationId: R106
      summary: 'R106 : Send Email Action'
      description: 'Sends an email with a validation token. Access: VST'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                e-mail:
                  type: string 
              required:
                - email
               
      responses:
        '200':
          description: 'Success you received a token on your email.'
        '404':
          description: 'Error. Email doesnt exists.'
          
######### RECOVER PASSWORD #########
  
  /recoverPassword:

    post:
      operationId: R107
      summary: 'R107 : Recover Password Action'
      description: 'Changes the current password after receiving the validation token. Access: VST'
      tags:
        - 'M01: Authentication'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                token:
                  type: string
                password:
                  type: string
                  format: password
                verify_password:
                  type: string
                  format: password
              required:
                - token
                - password
                - verify_password;

      responses:
        '200':
          description: 'Success. Your password has been changed successfully.'
        '404':
          description: 'Error. Invalid token.'

############################################ USERS ############################################

######### PROFILE #########

  /user/{id}:

    get:
      operationId: R201
      summary: 'R201: View user profile'
      description: 'Show the profile for an individual user, Access: USR, ADM, VST, OWN'
      tags:
        - 'M02: Users'

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: True

      responses:
        '200':
          description: 'OK. Show the profile for an individual user'
        '302':
          description: 'Redirect if user is not logged in or other user doesnt exists'
          headers:
            Location:
              schema:
                type: string
              example:
                302Failure:
                  description: 'Failure.'

######### HOME PAGE #########

  /home:

    get:
      operationId: R202
      summary: 'R202: View user home page'
      description: 'Show user home page, Access: USR, ADM'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the home page for an individual user'
        '302':
          description: 'Redirect after unauthorized request.'
          headers:
            Location:
              schema:
                type: string
              example:
                  302Success:
                    description: 'You need login first. Redirect to login page.'
                    value: '/login'

######### EDIT PROFILE #########

  /user/edit:

    get:
      operationId: R203
      summary: 'R203: Edit user profile page.'
      description: 'Shows the edit profile page of the user. Access: OWN'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'Ok. You can now edit. Show edit profile UI.'
        '401':
          description: 'Unauthorized. You cannot edit this profile.'
          headers:
            Location:
              schema:
                type: string
              examples:
                401Success:
                  description: 'Unauthorized. Redirect to user profile.'
                  value: '/user/{id}'

    post:
      operationId: R204
      summary: 'R204: Edit user profile action'
      description: 'Processes and saves the changes made by user. Access: USR'
      tags:
        - 'M02: Users'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                name:
                  type: string
                username:
                  type: string
                email:
                  type: string
                  format: email
                description:
                  type: string
                  format: password
                password:
                  type: string
                  format: password
                confirm_password:
                  type: string
                  format: password
                image:
                  type: string
                  format: binary
                is_public:
                  type: boolean

              required:
              - name
              - username
              - email
              - description
              - is_public

      responses:
        '302':
          description: 'Redirect after processing the new user information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful update. Redirect to user profile page.'
                  value: '/user/{id}'
                302Failure:
                  description: 'Failed update. Redirect again to edit profile page.'
                  value: '/user/edit'

######### PROFILE DELETE #########

  /user/profileDelete:

    post:
      operationId: R205
      summary: 'R205: Deletes an user.'
      description: 'Deletes an user while in the profile page. Access: OWN ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '302':
          description: 'Redirect after deleting user information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful delete. Redirect to login page.'
        '403':
          description: 'Forbiden action.'

######### DELETE #########

  /user/delete:

    post:
      operationId: R206
      summary: 'R206: Deletes an user.'
      description: 'Deletes usero while on admin page. Access: ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You can delete your profile information.'
        '403':
          description: 'Forbiden action.'

######### REMOVE FOLLOWER #########

  /user/removeFollower:

    post:
      operationId: R207
      summary: 'R207: Removes a follower.'
      description: 'Removes a follower from the user. Access: OWN'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You can remove a follower from your profile.'
        '403':
          description: 'Forbiden action.'
    
######### FOLLOW #########

  /user/follow:

    post:
      operationId: R208
      summary: 'R208: Follows another user.'
      description: 'Follows another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You followed a user.'
        '403':
          description: 'Forbiden action.'

######### UNFOLLOW #########

  /user/unfollow:

    post:
      operationId: R209
      summary: 'R208: Unfollows another user.'
      description: 'Unfollows another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You unfollowed a user.'
        '403':
          description: 'Forbiden action.'

######### DO FOLLOW REQUEST #########

  /user/doFollowRequest:

    post:
      operationId: R210
      summary: 'R210: Sends a follow request to another user.'
      description: 'Sends a notification with a follow request to another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You sent a follow request to a user.'
        '403':
          description: 'Forbiden action.'

######### CANCEL FOLLOW REQUEST #########

  /user/cancelFollowRequest:

    post:
      operationId: R211
      summary: 'R211: Cancels a follow request to another user.'
      description: 'Removes the notification of the follow request to other user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You canceled the follow request.'
        '403':
          description: 'Forbiden action.'

######### ACCEPT FOLLOW REQUEST #########

  /user/acceptFollowRequest:

    post:
      operationId: R212
      summary: 'R212: Accept a follow request.'
      description: 'Accepts a follow request from another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You accepted a follow request.'
        '403':
          description: 'Forbiden action.'

######### REJECT FOLLOW REQUEST #########

  /user/rejectFollowRequest:

    post:
      operationId: R213
      summary: 'R213: Reject a follow request.'
      description: 'Rejects a follow request from another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
          required: true
          content:
            application/x-www-form-urlencoded:
              schema:
                type: object
                properties:
                  id:
                    type: integer
                required:
                  - id

      responses:
        '200':
          description: 'Ok. You rejected a follow request.'
        '403':
          description: 'Forbiden action.'

######### NOTIFICATIONS #########

  /home/notifications:

    get:
      operationId: R214
      summary: 'R214: User notifications page.'
      description: 'Show user notifications page. Access: USR, ADM'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the user notifications page.'
        '302':
          description: 'Redirect if user is not logged in'
          headers:
            Location:
              schema:
                type: string
              example:
                302Failure:
                  description: 'Failure. User not logged in.'

######### NOTIFICATION DELETE #########

  /notification/delete:

    post:
      operationId: R215
      summary: 'R215: Delete a notification.'
      description: 'Deletes a notification per user request. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                - id

      responses:
        '200':
          description: 'Ok. Notification successfully deleted.'
        '403':
          description: 'Forbiden action.'

######### NOTIFICATION UPDATE #########

  /notification/update:

    post:
      operationId: R216
      summary: 'R216: Updates a notification.'
      description: 'Updates the notification info. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                - id

      responses:
        '200':
          description: 'Ok. Notification successfully deleted.'
        '403':
          description: 'Forbiden action.'

######### MESSAGES #########

  /messages:

    get:
      operationId: R217
      summary: 'R217: User chats page.'
      description: 'Show user chats page. Access: USR, ADM'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the user chats page.'
        '302':
          description: 'Redirect if user is not logged in'
          headers:
            Location:
              schema:
                type: string
              example:
                302Failure:
                  description: 'Failure. User not logged in.'

######### MESSAGE #########

  /message/{id}:

    get:
      operationId: R218
      summary: 'R218: Show chat with a user.'
      description: 'Shows the chat with another user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: True

      responses:
        '200':
          description: 'OK. Show the chat for an individual user'
        '302':
          description: 'Redirect if user is not logged in or other user doesnt exists'
          headers:
            Location:
              schema:
                type: string
              example:
                302Failure:
                  description: 'Failure.'     

######### MESSAGE CREATE #########

  /message/create:

    post:
      operationId: R219
      summary: 'R219: Sends a new message to a user.'
      description: 'Sends a new message to a user. Access: USR, ADM'
      tags:
        - 'M02: Users'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                user_id:
                  type: integer
                content:
                  type: string
                media:
                  type: string
                  format: binary
              required:
                - user_id

      responses:
        '302':
          description: 'Redirect after processing the new message sent.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful message create.'
                302Failure:
                  description: 'Error. No content and image found.'

######### ABOUT PAGE #########

  /about:

    get:
      operationId: R220
      summary: 'R220: About static page'
      description: 'Show About Page. Access USR, ADM, VST'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the about page.'

######### HELP PAGE #########

  /help:

    get:
      operationId: R221
      summary: 'R221: Help static page'
      description: 'Show Help Page. Access USR, ADM, VST'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the help page.'

######### FEATURES PAGE #########

  /features:

    get:
      operationId: R222
      summary: 'R222: Main features static page'
      description: 'Show OnlyFEUP main features. Access USR, ADM, VST'
      tags:
        - 'M02: Users'

      responses:
        '200':
          description: 'OK. Show the features page.'

######### MEDIA #########

  /images/{type}:

    get:
      operationId: R223
      summary: 'R223: Get media'
      description: 'Show OnlyFEUP media type. Access USR, ADM'
      tags:
        - 'M02: Users'

      parameters:
      - in: path
        name: type
        schema:
          type: string
        required: True

      responses:
        '200':
          description: 'OK. Show this type of media'
        '403':
          description: 'Forbiden action. You dont have permission to see this media'

############################################ POSTS ############################################

######### CREATE POST #########

  /post/create:

    post:
      operationId: R301
      summary: 'R301: Create post action'
      description: 'Create post. Access: USR'
      tags:
        - 'M03: Posts'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:
                  type: string
                media:
                  type: string
                  format: binary
                group_id:
                  type: integer

      responses:
        '302':
          description: 'Redirect after processing the new post information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful post create. Redirect back.'
                302Failure:
                  description: 'Failed. Redirect back.'

######### DELETE POST #########

  /post/delete:

    post:
      operationId: R302
      summary: 'R302 : Delete post action'
      description: 'Delete post. Access: OWN, ADM'
      tags:
        - 'M03: Posts'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id

      responses:
        '200':
          description: 'Redirect after processing the new post information.'
        '403':
          description: 'Forbiden action.'

######### EDIT POST #########

  /post/edit:

    post:
      operationId: R303
      summary: 'R303: Edit post action'
      description: 'Edit post. Access: OWN'
      tags:
        - 'M03: Posts'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
                content:
                  type: string
                image:
                  type: string
                  format: binary
                is_public:
                  type: boolean
              required:
                  - id
                  - content

      responses:
        '200':
          description: 'Edit successfully.'
        '403':
          description: 'Forbiden action.'

######### POST LIKE #########

  /post/like:

    post:
      operationId: R304
      summary: 'R304: Like post action'
      description: 'Like post. Access: OWN, USR, ADM'
      tags:
        - 'M03: Posts'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id
  
      responses:
        '200':
          description: 'Like successfully.'
        '403':
          description: 'Forbiden action.'

######### POST DISLIKE #########

  /post/dislike:

    post:
      operationId: R305
      summary: 'R305: Unlike post action'
      description: 'Unlike post. Access: OWN, USR, ADM'
      tags:
        - 'M03: Posts'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id

      responses:
        '200':
          description: 'Unlike successfully.'
        '403':
          description: 'Forbiden action.'

############################################ SEARCH ############################################

######### SEARCH PAGE #########

  /home/search:

    get:
      operationId: R401
      summary: 'R401: View user search page'
      description: 'Show user search page, Access: USR, ADM'
      tags:
        - 'M04: Search'

      responses:
        '200':
          description: 'OK. Show the search page UI'
        '302':
          description: 'Redirect after unauthorized request.'
          headers:
            Location:
              schema:
                type: string
              example:
                 - 302Success:
                    description: 'You need login first. Redirect to login page.'
                    value: '/login'

######### SEARCH USER #########

  /api/user:

    get:
      operationId: R402
      summary: 'R402 : Search users'
      description: 'Search users. Access: USR, ADM'
      tags:
        - 'M04: Search'

      parameters:
        - in: query
          name: search
          description: 'Search content'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of users information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

######### SEARCH POST #########

  /api/post:

    get:
      operationId: R403
      summary: 'R403 : Search posts'
      description: 'Search posts. Access: USR, ADM'
      tags:
        - 'M04: Search'

      parameters:
        - in: query
          name: content
          description: 'Search content'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of posts information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

######### SEARCH COMMENT #########

  /api/comment:

    get:
      operationId: R404
      summary: 'R404 : Search comments'
      description: 'Search comments. Access: USR, ADM'
      tags:
        - 'M04: Search'

      parameters:
        - in: query
          name: content
          description: 'Search content'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of comments information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

######### SEARCH GROUPS #########

  /api/group:

    get:
      operationId: R405
      summary: 'R405 : Search groups'
      description: 'Search groups. Access: USR, ADM'
      tags:
        - 'M04: Search'

      parameters:
        - in: query
          name: content
          description: 'Search content'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of groups information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

############################################ ADMINISTRATION ############################################

######### ADMIN PAGE #########

  /admin:

    get:
      operationId: R501
      summary: 'R501: View admin page'
      description: 'Show admin page UI, Access: ADM'
      tags:
        - 'M05: Administration'

      responses:
        '200':
          description: 'OK. Show the admin page UI'
        '403':
          description: 'This action is unauthorized.'

######### USER BLOCK #########

  /admin/user/block:

    post:
      operationId: R502
      summary: 'R502: Block a user from logging in action'
      description: 'Block a user. Access: ADM'
      tags:
        - 'M05: Administration'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                user_id:
                  type: integer
              required:
                - user_id

      responses:
        '200':
          description: 'Ok. User blocked.'
        '401':
          description: 'Unauthorized. You cannot block this user.'

######### USER UNBLOCK #########

  /admin/user/unblock:

    post:
      operationId: R503
      summary: 'R503: Unblocking a user from logging in action'
      description: 'Unblock a user. Access: ADM'
      tags:
        - 'M05: Administration'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                user_id:
                  type: integer
              required:
                - user_id

      responses:
        '200':
          description: 'Ok. User unblocked.'
        '401':
          description: 'Unauthorized. You cannot unblock this user.'

############################################ COMMENTS ##############################################

######### CREATE COMMENT #########

  /comment/create:

    post:
      operationId: R601
      summary: 'R601: Create comment action'
      description: 'Create comment. Access: USR'
      tags:
        - 'M06: Comments'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                content:
                  type: string
                group_id:
                  type: integer
                previous:
                  type: integer

      responses:
        '302':
          description: 'Redirect after processing the new comment information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful comment create. Redirect back.'
                302Failure:
                  description: 'Failed. Redirect back.'

######### DELETE COMMENT #########

  /comment/delete:

    post:
      operationId: R602
      summary: 'R602 : Delete comment action'
      description: 'Delete comment. Access: OWN, ADM'
      tags:
        - 'M06: Comments'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id

      responses:
        '200':
          description: 'Redirect after processing the old comment information.'
        '403':
          description: 'Forbiden action.'

######### EDIT COMMENT #########

  /comment/edit:

    post:
      operationId: R603
      summary: 'R603: Edit comment action'
      description: 'Edit comment. Access: OWN'
      tags:
        - 'M06: Comments'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
                content:
                  type: string

      responses:
        '200':
          description: 'Edit successfully.'
        '403':
          description: 'Forbiden action.'

######### LIKE COMMENT #########

  /comment/like:

    post:
      operationId: R604
      summary: 'R604: Like comment action'
      description: 'Like comment. Access: OWN, USR, ADM'
      tags:
        - 'M06: Comments'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id
  
      responses:
        '200':
          description: 'Like successfully.'
        '403':
          description: 'Forbiden action.'

######### COMMENT DISLIKE #########

  /comment/dislike:

    post:
      operationId: R605
      summary: 'R605: Unlike comment action'
      description: 'Unlike comment. Access: OWN, USR, ADM'
      tags:
        - 'M06: Comments'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                id:
                  type: integer
              required:
                  - id

      responses:
        '200':
          description: 'Unlike successfully.'
        '403':
          description: 'Forbiden action.'

############################################ GROUPS ############################################

################## GROUP PAGE ####################

  /group/{id}:

    get:
      operationId: R701
      summary: 'R701: View group page'
      description: 'Show the group page for an individual user, Access: USR, ADM, VST, OWN'
      tags:
        - 'M07: Groups'

      parameters:
        - in: path
          name: id
          schema:
            type: integer
          required: True

      responses:
        '200':
          description: 'OK. Show the group page'
        '302':
          description: 'Redirect if user is not logged in or group doesnt exists'
          headers:
            Location:
              schema:
                type: string
              example:
                302Failure:
                  description: 'Failure'

################## ALL GROUPS PAGE ####################

  /groups:

    get:
      operationId: R702
      summary: 'R702: View all groups page'
      description: 'Show groups page. Access: USR, ADM'
      tags:
        - 'M07: Groups'

      responses:
        '200':
          description: 'OK. Show the groups page for an individual user'
        '302':
          description: 'Redirect after unauthorized request.'
          headers:
            Location:
              schema:
                type: string
              example:
                  302Success:
                    description: 'You need login first. Redirect to login page.'
                    value: '/login'

################## GROUP EDIT PAGE ####################

  /group/{id}/edit:

    get:
      operationId: R703
      summary: 'R703: Edit group'
      description: 'Shows the edit group page. Access: OWN'
      tags:
        - 'M07: Groups'

      parameters:
      - in: query
        name: id
        description: 'Group ID'
        schema:
          type: integer
        required: true

      responses:
        '200':
          description: 'Ok. You can now edit. Show edit group UI.'
        '401':
          description: 'Unauthorized. You cannot edit this group.'
          headers:
            Location:
              schema:
                type: string
              examples:
                401Success:
                  description: 'Unauthorized. Redirect to group page.'
                  value: '/group/{id}'

################## GROUP EDIT ####################

  /group/edit:
    
    post:
      operationId: R704
      summary: 'R704: Edit group action'
      description: 'Processes and saves the changes made by group owner. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                name:
                  type: string
                description:
                  type: string
                  format: password
                image:
                  type: string
                  format: binary
                is_public:
                  type: boolean

      responses:
        '302':
          description: 'Redirect after processing the new group information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful update. Redirect to group page.'
                  value: '/group/{id}'
                302Failure:
                  description: 'Failed update. Redirect again to edit group page.'
                  value: '/group/{id}/edit'

################## GROUP CREATE ####################

  /group/create:
    
    post:
      operationId: R705
      summary: 'R705: Create group action'
      description: 'Processes and saves new group state. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                name:
                  type: string
                description:
                  type: string
                  format: password
                image:
                  type: string
                  format: binary
                is_public:
                  type: boolean

      responses:
        '302':
          description: 'Redirect after processing the new group information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful create. Redirect to group page.'
                  value: '/group/{id}'
                302Failure:
                  description: 'Failed. Redirect again to previous page'

################## GROUP JOIN ####################

  /group/join:

    post:
      operationId: R706
      summary: 'R706: Joining group with'
      description: 'Joining group. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Request complete.'
        '401':
          description: 'Unauthorized. You cannot request to join this group.'

################## GROUP LEAVE ####################

  /group/leave:

    post:
      operationId: R707
      summary: 'R707: Leave group'
      description: 'Leave group. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Leave group complete.'
        '401':
          description: 'Unauthorized. You cannot leave this group'

################## GROUP DELETE ####################

  /group/delete:

    post:
      operationId: R708
      summary: 'R708: Group delete action'
      description: 'Delete group. Access: OWN, ADM'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
              required:
                - group_id

      responses:
        '200':
          description: 'Ok. Deleted complete.'
        '401':
          description: 'Unauthorized. You cannot delete this group.'

################## GROUP OWNERSHIP ####################

  /group/makeOwner:

    post:
      operationId: R709
      summary: 'R709: Give group ownership of a certain member'
      description: 'Give group ownership of a certain member. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '302':
          description: 'Redirect after processing the new group ownership.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful. Redirect to group page with new owner.'
                  value: '/group/{id}'
                302Failure:
                  description: 'Failed. Redirect again to previous page without changing anything'

################## GROUP JOIN REQUEST ####################

  /group/doJoinRequest:

    post:
      operationId: R710
      summary: 'R710: Group join request'
      description: 'Group join request. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Join request complete.'
        '401':
          description: 'Unauthorized. You cannot join request this group.'

################## GROUP CANCEL JOIN REQUEST ####################

  /group/cancelJoinRequest:

    post:
      operationId: R711
      summary: 'R711: Cancel group join request'
      description: 'Cancel group join request. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Cancel request complete.'
        '401':
          description: 'Unauthorized. You cannot cancel this join request'

################## GROUP ACCEPT JOIN REQUEST ####################

  /group/acceptJoinRequest:

    post:
      operationId: R712
      summary: 'R712: Accept group join request'
      description: 'Accept group join request. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Accepted request complete.'
        '401':
          description: 'Unauthorized. You cannot accept this join request'

################## GROUP REJECT JOIN REQUEST ####################

  /group/rejectJoinRequest:

    post:
      operationId: R713
      summary: 'R713: Reject group join request'
      description: 'Reject group join request. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Reject request complete.'
        '401':
          description: 'Unauthorized. You cannot reject this join request'

################## GROUP REMOVE MEMBER ####################

  /group/removeMember:

    post:
      operationId: R714
      summary: 'R714: Remove member action'
      description: 'Remove group member. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Action complete.'
        '401':
          description: 'Unauthorized. You cannot remove this member'

################## GROUP INVITE ####################

  /group/invite:

    post:
      operationId: R715
      summary: 'R715: Invite someone to the group'
      description: 'Invite request. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Invitation complete.'
        '401':
          description: 'Unauthorized. You cannot invite this user'

################## GROUP CANCEL INVITE ####################

  /group/cancelInvite:

    post:
      operationId: R716
      summary: 'R716: Cancel Invitation'
      description: 'Cancel Invitation. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Invitation canceled.'
        '401':
          description: 'Unauthorized. You cannot cancel this invitation'

################## GROUP REJECT INVITE ####################

  /group/rejectInvite:

    post:
      operationId: R717
      summary: 'R717: Reject invitation'
      description: 'Reject invitation. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Invitation rejected.'
        '401':
          description: 'Unauthorized. You cannot reject this invitation'

################## GROUP ACCEPT INVITE ####################

  /group/acceptInvite:

    post:
      operationId: R718
      summary: 'R718: Accept invitation'
      description: 'Accept invitation. Access: USR'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. Invitation accepted.'
        '401':
          description: 'Unauthorized. You cannot accept this invitation'

################## GROUP FAVORITE ####################

  /group/favorite:

    post:
      operationId: R719
      summary: 'R719: Group favorite'
      description: 'Favorite a certain group. Access: USR, OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. This group is now one of favorites.'
        '401':
          description: 'Unauthorized. You cannot favorite this group'

################## GROUP UNFAVORITE ####################

  /group/unfavorite:

    post:
      operationId: R720
      summary: 'R720: Group unfavorite'
      description: 'Undo group favorite. Access: USR, OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
                user_id: 
                  type: integer
              required:
                - user_id
                - group_id

      responses:
        '200':
          description: 'Ok. This group is not now one of favorites.'
        '401':
          description: 'Unauthorized. You cannot unfavorite this group'

################## GROUP DELETE MEDIA ####################

  /group/deleteMedia:

    post:
      operationId: R721
      summary: 'R721: Delete group media'
      description: 'Delete group media. Access: OWN'
      tags:
        - 'M07: Groups'

      requestBody:
        required: true
        content:
          application/x-www-form-urlencoded:
            schema:
              type: object
              properties:
                group_id:
                  type: integer
              required:
                - group_id

      responses:
        '302':
          description: 'Redirect after processing the new group information.'
          headers:
            Location:
              schema:
                type: string
              example:
                302Success:
                  description: 'Successful. Redirect to group page without picture.'
                  value: '/group/{id}'
                302Failure:
                  description: 'Failed. Redirect again to previous page without changing anything'

############################################ API ############################################

################## USER VERIFY ####################

  /api/userVerify:

    get:
      operationId: R801
      summary: 'R801 : Verify username'
      description: 'Verify if username exists. Access: USR, ADM'
      tags:
        - 'M08: API'

      parameters:
        - in: query
          name: username
          description: 'Username attemp'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns an ID of correspondent username'
        '403':
          description: 'Forbiden action. You need to be logged in first'

################## NOTIFICATIONS ####################

  /api/notifications:

    get:
      operationId: R802
      summary: 'R802 : Notifications'
      description: 'Get user notifications. Access: USR'
      tags:
        - 'M08: API'

      parameters:
        - in: query
          name: id
          description: 'Notification type: post, comment, user, group, all'
          schema:
            type: string
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of notifications information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

################## NOTIFICATION CONTEXT ####################

  /api/context:

    get:
      operationId: R803
      summary: 'R803 : Notification context'
      description: 'Get notification context. Access: USR, ADM'
      tags:
        - 'M08: API'

      parameters:
        - in: query
          name: id
          description: 'Notification id'
          schema:
            type: integer
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing notification context (post, comment, subcomment) information'
        '403':
          description: 'Forbiden action. You need to be logged in first'

################## MESSAGES ####################

  /api/messages:

    get:
      operationId: R804
      summary: 'R804 : Private messages'
      description: 'Get new private messages with certain user. Access: USR, ADM'
      tags:
        - 'M08: API'

      parameters:
        - in: query
          name: id
          description: 'Target User ID'
          schema:
            type: integer
          required: true

      responses:
        '200':
          description: 'Success. Returns some HTML text containing a list of new messages information'
        '403':
          description: 'Forbiden action. You need to be logged in first'
```

---

## A8: Vertical Prototype

The Vertical Prototype includes the implementation of the features marked as necessary in the common and theme requirements documents. By that information, we implemented all the user stories with priority high as we can see in the sections below. \
The objective of this artifact is to validate the architecture presented and for us to get basic knowledge of the technologies used in the project.\
As recommended, the implementation is based on the code of [LBAW Framework](https://git.fe.up.pt/lbaw/template-laravel) and includes work on all layers of the architecture of the solution to implement. The prototype includes the implementation of the visualization of pages (as home, profile, admin and search pages), insertion, edition and removal of content (posts) and some error and success messages.

## 1. Implemented Features

### 1.1. Implemented User Stories

<table>
<tr>
<td>

**User Story**
</td>
<td>

**Name**
</td>
<td>

**Priority**
</td>
<td>

**Description**
</td>
</tr>
<tr>
<td>US01</td>
<td>See Home Page</td>
<td>high</td>
<td>As a User, I want to see the Home Page so that I can start navigating the site.</td>
</tr>
<tr>
<td>US02</td>
<td>View Public Timeline</td>
<td>high</td>
<td>As a User, I want to be able to view thePublic Timeline so that I can keep up with what’s happening.</td>
</tr>
<tr>
<td>US03</td>
<td>View Public Profiles</td>
<td>high</td>
<td>As a User, I want to be able to view public profiles so that I can see their posts and information.</td>
</tr>
<tr>
<td>US04</td>
<td>Search Public Users</td>
<td>high</td>
<td>As a User, I want to be able search for public users so that I can view their profiles.</td>
</tr>
<tr>
<td>US08</td>
<td>Sign-up</td>
<td>high</td>
<td>

As a _Visitor_, I want to register myself into the system, so that I can authenticate myself into the system
</td>
</tr>
<tr>
<td>US09</td>
<td>Sign-in</td>
<td>high</td>
<td>

As a _Visitor_, I want to authenticate into the system, so that I can access privileged information
</td>
</tr>
<tr>
<td>US10</td>
<td>Sign-out</td>
<td>high</td>
<td>

As an _Authenticated User_, I want to sign out, so that I am not logged in anymore.
</td>
</tr>
<tr>
<td>US11</td>
<td>View personalized timeline</td>
<td>high</td>
<td>As an Authenticated User, I want to view a personalized timeline, so that I can view posts that I actually have an interest in.</td>
</tr>
<tr>
<td>US12</td>
<td>Create Post</td>
<td>high</td>
<td>

As an _Authenticated User_, I want to create posts, so that I can share information to my followers.
</td>
</tr>
<tr>
<td>US40</td>
<td>Edit post</td>
<td>high</td>
<td>

As a _Post Author_, I want to edit my post, so that I can correct a mistake I made.
</td>
</tr>
<tr>
<td>US41</td>
<td>Delete post</td>
<td>high</td>
<td>

As a _Post Author_, I want to delete a post, so that I can remove something that I put by mistake.
</td>
</tr>
<tr>
<td>US45</td>
<td>Special Search privileges</td>
<td>high</td>
<td>As an Administrator, I want to search for profiles and groups even if they are private, so that I can investigate if there is something against the guidelines.</td>
</tr>
</table>

\
Table 64: Vertical Prototype implemented user stories

### 1.2. Implemented Web Resources

#### Module M01: Authentication

<table>
<tr>
<td>

**Web Resource Reference**
</td>
<td>

**URL**
</td>
</tr>
<tr>
<td>R101: Login Form</td>
<td>

GET [/login](https://lbaw2255.lbaw.fe.up.pt/login)
</td>
</tr>
<tr>
<td>R102: Login Action</td>
<td>POST /login</td>
</tr>
<tr>
<td>R103: Logout Action</td>
<td>

GET [/logout](https://lbaw2255.lbaw.fe.up.pt/logout)
</td>
</tr>
<tr>
<td>R104: Register Form</td>
<td>

GET [/register](https://lbaw2255.lbaw.fe.up.pt/register)
</td>
</tr>
<tr>
<td>R105: Register Action</td>
<td>POST /register</td>
</tr>
</table>

Table 65: Authentication implementation

#### Module M02: Users

<table>
<tr>
<td>

**Web Resource Reference**
</td>
<td>

**URL**
</td>
</tr>
<tr>
<td>R201: View user profile</td>
<td>

GET [/user/{id}](https://lbaw2255.lbaw.fe.up.pt/user/1)
</td>
</tr>
<tr>
<td>R202: View user home page</td>
<td>

GET [/home](https://lbaw2255.lbaw.fe.up.pt/home)
</td>
</tr>
<tr>
<td>R203: Edit user profile page</td>
<td>

GET [/user/edit](https://lbaw2255.lbaw.fe.up.pt/user/edit)
</td>
</tr>
<tr>
<td>R204: Edit user profile action</td>
<td>POST /user/edit</td>
</tr>
</table>

Table 66: Users implementation

#### Module M03: Posts

<table>
<tr>
<td>

**Web Resource Reference**
</td>
<td>

**URL**
</td>
</tr>
<tr>
<td>R301: Create post action</td>
<td>POST /post/create</td>
</tr>
<tr>
<td>R302: Delete post action</td>
<td>POST /post/delete</td>
</tr>
<tr>
<td>R303: Delete post media action</td>
<td>POST /post/deletemedia</td>
</tr>
<tr>
<td>R304: Edit post action</td>
<td>POST /post/edit</td>
</tr>
</table>

Table 67: Posts implementation

#### Module M04: Search

<table>
<tr>
<td>

**Web Resource Reference**
</td>
<td>

**URL**
</td>
</tr>
<tr>
<td>R401: View user search page</td>
<td>

GET [/home/search](https://lbaw2255.lbaw.fe.up.pt/home/search)
</td>
</tr>
<tr>
<td>R402: Search users</td>
<td>

GET [/api/user](https://lbaw2255.lbaw.fe.up.pt/api/user)
</td>
</tr>
<tr>
<td>R403: Search posts</td>
<td>

GET [/api/post](https://lbaw2255.lbaw.fe.up.pt/api/post)
</td>
</tr>
</table>

Table 68: Search implementation

#### Module M05: Administration

<table>
<tr>
<td>

**Web Resource Reference**
</td>
<td>

**URL**
</td>
</tr>
<tr>
<td>R501: View admin page</td>
<td>

GET [/admin](https://lbaw2255.lbaw.fe.up.pt/admin)
</td>
</tr>
<tr>
<td>R502: Block a user from logging in action</td>
<td>POST /admin/user/block</td>
</tr>
<tr>
<td>R503: Unblocking a user from logging in action</td>
<td>POST /admin/user/unblock</td>
</tr>
<tr>
<td>R504: Ban a user action</td>
<td>POST /admin/user/ban</td>
</tr>
</table>

Table 69: Administration implementation

## 2. Changes to database
 - Two new columns added to user table: *description* and *remember_token*
 - Changed passwords encryption method to bcrypt
 - Media table and its references deleted
 - Removed triggers related to media
 - Two new trigger: *delete_post_action* and *delete_comment_action*
 - New table for blocked users: *blocked*

## 3. Prototype

For this prototype we focused our efforts in developing the main functionalities of the project. We did not focus too much on the visual aspect, so the design is not perfected, but enough to get an idea of the general layout and easily navigate through the website.

The prototype is available at http://lbaw2255.lbaw.fe.up.pt
**Note:** The prototype is no longer available since modification to the system were already deployed**

Credentials:

* admin user: eduardanascimento@gmail.com | eduardalbaw2255
* regular user: laravel@hotmail.com | password

The code is available at https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255 ---

---

## Revision history

### Artifacts: A7

#### Editor: Lourenço Gonçalves

> **November 3:** \
>   Resources overview and permissions. Started working on OpenAPI resource documentation. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 10:** \
>   A7 finished. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 21:** \
>   Corrections of A7. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

### Artifacts: A8

#### Editor: André Costa

> **November 14:** \
>   Login and Logout features implemented. Showing public feed. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 15:** \
>   Added "remember me" to login form. Register done. Create and delete post done. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 16:** \
>   Edit post done. User profile page started. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 17:** \
>   Default picture added. Remove table media. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 18:** \
>   Profile now show number of followers, followed users and posts. Posts show number of likes and comments. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 19:** \
>   Upload and delete images in posts. Image routes. Edit user done. Started working on admin features. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 20:** \
>   Admin search finished. Admin can block user and delete posts. User search done. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

> **November 21:** \
>   Bug fixes. Some CSS corrections. Deployed image. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto

### Changes to database

> - 2 Columns added to users table: 'description' and 'remember_token'.
> - Changed passwords encryption method to bcrypt.
> - Media table and its references deleted.
> - Removed triggers related to media.
> - 2 new triggers: 'delete_post_action' and 'delete_comment_action'.
> - New table for blocked users: 'blocked'.

---

### Group lbaw2255, 21/11/2022