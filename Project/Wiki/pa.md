# PA: Product and Presentation

## A9: Product

The OnlyFEUP final product is the result of the implementation of the information system designed in the previous stages (A1 to A7), using PHP and Laravel Framework to produce dynamic web pages, AJAX for a better user experience and PostgreSQL as a database.

The main goal of the OnlyFEUP project is the development of a web-based social network with the purpose of creating connections between students and staff, sharing resources about courses and subjects. This is a tool that can be used by anyone from FEUP. After signing up and verifying the user is related to the university (students/teachers), they can start using it for a better experience at FEUP.

### Installation

The release with the final version of the source code in the group's Git repository is available[ here](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/tags), in PA tag.

Full Docker command to launch the image available in the group's GitLab Container Registry using the production database:

```
docker run -it -p 8000:80 --name=lbaw2255 -e DB_DATABASE="lbaw2255" -e DB_SCHEMA="lbaw2255" -e DB_USERNAME="lbaw2255" -e DB_PASSWORD="reWisDQE" git.fe.up.pt:5050/lbaw/lbaw2223/lbaw2255
```

### Usage

The final product is available online on https://lbaw2255.lbaw.fe.up.pt

#### Administration Credentials

<table>
<tr>
<td>

**Email**
</td>
<td>

**Password**
</td>
</tr>
<tr>
<td>eduardanascimento@gmail.com</td>
<td>eduardalbaw2255</td>
</tr>
</table>

Table 89: OnlyFEUP Administration Credentials

#### User Credentials

<table>
<tr>
<td>

**Email**
</td>
<td>

**Password**
</td>
</tr>
<tr>
<td>laravel@hotmail.com</td>
<td>password</td>
</tr>
</table>

Table 90: OnlyFEUP User Credentials

### Application Help

Features related to Help were implemented as long as the rest of the main features. It can be visualized on alert messages on some actions and on some static pages, like “HELP”, “About us” and “Features”.

You can access these pages by their url path, like “/help”, “/about” and “/features” or by pressing the buttons that we created to access these pages. These buttons are always placed in the sidebar.

![image_2023-01-02_183103153](uploads/2b2c503bfc88c835cd88a9d4db25f5d9/image_2023-01-02_183103153.png)

Figure 7: OnlyFEUP static pages

\
On the “HELP!” page we have two main sections. The “frequently asked questions” section presents some usual questions asked and their answers and the “contacts” section shows the administrators’ information so that users may contact them.

![OnlyFEUP help page](uploads/224301d160ab69c7b4b93b5c7b9f0f6f/image_2023-01-02_183134522.png)

Figure 8: OnlyFEUP help page

The “About us” page is useful to give some knowledge about our Social Media and about our staff.

![image_2023-01-02_183220441](uploads/f86939ae9fee46aa9f883710c169841b/image_2023-01-02_183220441.png)

Figure 9: OnlyFEUP AboutUs page

The “Features” page, like the name says, presents the main features implemented divided by topics.

![image_2023-01-02_183242128](uploads/0e5276aba879e3a3bc4f69e2e22a7bc1/image_2023-01-02_183242128.png)

Figure 10: OnlyFEUP Features page

As alert messages, we decided to implement error/success messages on actions like creating posts/comments and groups and some confirmation messages on relevant actions like deleting a group/account.

![image_2023-01-02_183301990](uploads/613de50694d7358381bc3c734a0b59db/image_2023-01-02_183301990.png)

Figure 11: Example of success messages

![image_2023-01-02_183321708](uploads/20d68ddb350d699e6a2ea3d9d11cb1fa/image_2023-01-02_183321708.png)

Figure 12: Group deletion confirmation message

### Input Validation

As the back-end input validation we used the Illuminate\\Http\\Request granting us access to a function called “validate” that has different types of validation. We used this feature to validate the inputs of our forms, like the login/register form, and the edit user/group profile form as we can see in the next examples:

![image_2023-01-02_183355290](uploads/7a67989de6c948ce717ce51317acab3e/image_2023-01-02_183355290.png)

Figure 13: Token validation in password recovery

![image_2023-01-02_183411905](uploads/dad33513b32b59cd5fb2ef8c0f00968a/image_2023-01-02_183411905.png)

Figure 14: Back-end input validation in edit user profile page

As the front-end input validation we used javascript. For example, when we edit an existing comment or post but remove the text completely, there is a warning:

![Front-end input](uploads/f86ed343824037313c06625f9ab68973/fileee.PNG)

Figure 15: Front-end input validation in edit an empty post

### Check Accessibility and Usability

[Accessibility Checklist](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/Accessibility%20Checklist.pdf), 18/18

[Usability Checklist](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/Usability%20Checklist.pdf), 27/28

### HTML and CSS Validation

[HTML Validation](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/HTML%20Validation.pdf), here the warnings and errors are the result of the bootstrap framework and because of the many posts and comments that repeats then.

[CSS Validation](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/CSS%20Validation.pdf)

### Revisions of the Project

There are many revisions made to the project since the requirements specification stage:

#### Database Schema

* Two attributes added to users table: 'description' and 'remember_token';
* Changed passwords encryption method to _bcrypt_;
* Media table and its references (in tables or triggers) deleted;
* New table for blocked users: 'blocked';
* New table for user notification configurations: ‘configuration’;
* New table for user private messages: ‘message’;
* New attribute for posts privacy: ‘is_public’;
* New attribute for comments replies: ‘previous’;
* Eight new types of notifications: ‘leave_group’, ‘invite’, ‘ban’, ‘group_ownership’, ‘post_tagging’, ‘comment_post’, ‘reply_comment’, ‘comment_tagging’;

#### Triggers

* Update trigger 10 'delete_post_action': when deleting a post it also deletes its comments, subcomments, likes and notifications (business rule BR17);
* Update trigger 11 ‘delete_comment_action’: when deleting a comment it also deletes its likes, subcomments and notifications (business rule BR18);
* New trigger 12 ‘delete_group_action’: when deleting a group it also deletes its posts, members, likes, comments, subcomments, notifications and group_notifications (business rule BR19)
* New trigger 13 ‘delete_mainnotification_action’: after deleting a subnotification, delete main notification table entry;
* New trigger 14 ‘configuration_action’: When new user appears, he initially gets all kinds of notification (business rule BR20);

#### Routes

* **/messages**, for user private messages;
* **/sendEmail**, for sending email with recover password token;
* **/recoverPassword**, for recover password action;
* **/user/doFollowRequest**,\*\* /user/cancelFollowRequest\*\*, **/user/acceptFollowRequest**, **/user/rejectFollowRequest**, for user following manipulation;
* **/api/comment**, for comment searching;
* **/api/notifications**, for notification management;
* **/api/context**, to get notification context;
* **/comment/like**, **/comment/dislike**,\*\* /comment/create\*\*, **/comment/delete**, **/comment/edit**, for comment management;
* **/message/{id}** and **/message/create** for private messages management;
* **/notification/delete** and **/notification/update** for user notifications management;
* **/images/{type}** for image privacy and visibility;

#### User Stories

About 30 new low-priority User Stories were added:

* **User functionalities**: ‘started following’ and ‘accepted follow request’ notifications, manage and delete notifications, see notification context,
* **Group functionalities:** ‘invited to group’, ‘ban’, ‘join’ and ‘new ownership’ notifications, join public and private groups, request to join in private groups, remove posts/comments from groups, change group visibility, manage group invitations, delete group, give group ownership, accept invites and leave group. Mark group as favorites,
* **Comment functionalities:** reply to comments, tag users in comments, new tagging user notification, edit comment, delete comment
* **Post functionalities:** post tagging notification, hashtags and searching for hashtags, manage post visibility, post media (images and videos),
* **Message functionalities:** see messages status, send private messages with text and/or media (images, videos and audios);

### Web Resources Specification

#### Implemented Web Resources

##### Module M01: Authentication

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
<tr>
<td>R106: Send Email Action</td>
<td>POST /sendEmail</td>
</tr>
<tr>
<td>R107: Recover Password Action</td>
<td>POST /recoverPassword</td>
</tr>
</table>

Table 91: Authentication implementation

##### Module M02: Users

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
<tr>
<td>R205: Profile Delete</td>
<td>POST /user/profileDelete</td>
</tr>
<tr>
<td>R206: Delete User</td>
<td>POST /user/delete</td>
</tr>
<tr>
<td>R207: Remove follower</td>
<td>POST /user/removeFollower</td>
</tr>
<tr>
<td>R208: Follow</td>
<td>POST /user/follow</td>
</tr>
<tr>
<td>R209: Unfollow</td>
<td>POST /user/unfollow</td>
</tr>
<tr>
<td>R210: Do Follow Request</td>
<td>POST /user/doFollowRequest</td>
</tr>
<tr>
<td>R211: Cancel Follow Request</td>
<td>POST /user/cancelFollowRequest</td>
</tr>
<tr>
<td>R212: Accept Follow Request</td>
<td>POST /user/acceptFollowRequest</td>
</tr>
<tr>
<td>R213: Reject Follow Request</td>
<td>POST /user/rejectFollowRequest</td>
</tr>
<tr>
<td>R214: User Notifications</td>
<td>

GET [/home/notifications](https://lbaw2255.lbaw.fe.up.pt/home/notifications)
</td>
</tr>
<tr>
<td>R215: Delete Notification</td>
<td>POST /notification/delete</td>
</tr>
<tr>
<td>R216: Update Notification</td>
<td>POST /notification/update</td>
</tr>
<tr>
<td>R217: Messages</td>
<td>

GET [/messages](https://lbaw2255.lbaw.fe.up.pt/messages)
</td>
</tr>
<tr>
<td>R218: Private Messages</td>
<td>

GET [/message/{id}](https://lbaw2255.lbaw.fe.up.pt/message/1)
</td>
</tr>
<tr>
<td>R219: Create Message</td>
<td>POST /message/create</td>
</tr>
<tr>
<td>R220: About page</td>
<td>

GET [/about](https://lbaw2255.lbaw.fe.up.pt/about)
</td>
</tr>
<tr>
<td>R221: Help page</td>
<td>

GET[ /help](https://lbaw2255.lbaw.fe.up.pt/help)
</td>
</tr>
<tr>
<td>R222: Features page</td>
<td>

GET [/features](https://lbaw2255.lbaw.fe.up.pt/features)
</td>
</tr>
<tr>
<td>R223: Images</td>
<td>

GET [/images/{type}](https://lbaw2255.lbaw.fe.up.pt/images/%7Btype%7D)
</td>
</tr>
</table>

Table 92: Users implementation

##### Module M03: Posts

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
<td>R303: Edit post action</td>
<td>POST /post/edit</td>
</tr>
<tr>
<td>R304: Like post action</td>
<td>POST /post/like</td>
</tr>
<tr>
<td>R305: Unlike post action</td>
<td>POST /post/unlike</td>
</tr>
</table>

Table 93: Posts implementation

##### Module M04: Search

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
<tr>
<td>R404: Search comments</td>
<td>

GET [/api/comment](https://lbaw2255.lbaw.fe.up.pt/api/comment)
</td>
</tr>
<tr>
<td>R405: Search groups</td>
<td>

GET [/api/group](https://lbaw2255.lbaw.fe.up.pt/api/group)
</td>
</tr>
</table>

Table 94: Search implementation

##### Module M05: Administration

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
</table>

Table 95: Administration implementation

##### Module M06: Comments

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
<td>R601: Create comment action</td>
<td>POST /comment/create</td>
</tr>
<tr>
<td>R602: Delete comment action</td>
<td>POST /comment/edit</td>
</tr>
<tr>
<td>R603: Edit comment action</td>
<td>POST /comment/edit</td>
</tr>
<tr>
<td>R604: Like comment action</td>
<td>POST /comment/like</td>
</tr>
<tr>
<td>R605: Unlike comment action</td>
<td>POST /comment/unlike</td>
</tr>
</table>

Table 96: Comments implementation

##### Module M07: Comments

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
<td>R701: Group page</td>
<td>

GET [/group/{id}](https://lbaw2255.lbaw.fe.up.pt/group/2)
</td>
</tr>
<tr>
<td>R702: All groups page</td>
<td>

GET [/groups](https://lbaw2255.lbaw.fe.up.pt/groups)
</td>
</tr>
<tr>
<td>R703: Edit group page</td>
<td>

GET [/group/{id}/edit](https://lbaw2255.lbaw.fe.up.pt/group/17/edit)
</td>
</tr>
<tr>
<td>R704: Edit group action</td>
<td>POST /group/edit</td>
</tr>
<tr>
<td>R705: Create group</td>
<td>POST /group/create</td>
</tr>
<tr>
<td>R706: Joining group</td>
<td>POST /group/join</td>
</tr>
<tr>
<td>R707: Group leave</td>
<td>POST /group/leave</td>
</tr>
<tr>
<td>R708: Group delete</td>
<td>POST /group/delete</td>
</tr>
<tr>
<td>R709: Give group ownership</td>
<td>POST /group/makeOwner</td>
</tr>
<tr>
<td>R710: Group join request</td>
<td>POST /group/doJoinRequest</td>
</tr>
<tr>
<td>R711: Cancel group join request</td>
<td>POST /group/cancelJoinRequest</td>
</tr>
<tr>
<td>R712: Accept Join Request</td>
<td>POST /group/acceptJoinRequest</td>
</tr>
<tr>
<td>R713: Reject Join Request</td>
<td>POST /group/rejectJoinRequest</td>
</tr>
<tr>
<td>R714: Remove member</td>
<td>POST /group/removeMember</td>
</tr>
<tr>
<td>R715: Invite</td>
<td>POST /group/invite</td>
</tr>
<tr>
<td>R716: Cancel Invite</td>
<td>POST /group/cancelInvite</td>
</tr>
<tr>
<td>R717: Reject Invite</td>
<td>POST /group/cancelInvite</td>
</tr>
<tr>
<td>R718: Accept Invite</td>
<td>POST /group/acceptInvite</td>
</tr>
<tr>
<td>R719: Favorite group</td>
<td>POST /group/favorite</td>
</tr>
<tr>
<td>R720: Unfavorite group</td>
<td>POST /group/unfavorite</td>
</tr>
<tr>
<td>R721: Delete group media</td>
<td>POST /group/deleteMedia</td>
</tr>
</table>

Table 97: Groups implementation

##### Module M08: API

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
<td>R801: Verify username</td>
<td>

GET [/api/userVerify](https://lbaw2255.lbaw.fe.up.pt/api/userVerify)
</td>
</tr>
<tr>
<td>R802: Notifications</td>
<td>

GET[ /api/notifications](https://lbaw2255.lbaw.fe.up.pt/api/notifications)
</td>
</tr>
<tr>
<td>R803: Notification context</td>
<td>

GET[ /api/context](https://lbaw2255.lbaw.fe.up.pt/api/context)
</td>
</tr>
<tr>
<td>R804: Private messages</td>
<td>

GET [/api/messages](lbaw2255.lbaw.fe.up.pt/api/messages)
</td>
</tr>
</table>

Table 98: Comments implementation

#### OpenAPI Specification

Full OpenAPI documentation for OnlyFEUP is available in the annexes to this document and in the [main repository](https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/a9_openapi.yaml).

### Implementation Details

#### Libraries and Frameworks Used

* [Laravel](https://laravel.com/), for server-side management
* [Bootstrap](https://getbootstrap.com/), for frontend responsive and intuitive
* [FontAwesome](https://fontawesome.com/), for icons and buttons

#### User Stories

<table>
<tr>
<td>

**ID**
</td>
<td>

**Name**
</td>
<td>

**Priority**
</td>
<td>

**Module**
</td>
<td>

**Team Members**
</td>
<td>

**State**
</td>
</tr>
<tr>
<td>US01</td>
<td>See Home Page</td>
<td>high</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US02</td>
<td>View Public Timeline</td>
<td>high</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US03</td>
<td>View Public Profiles</td>
<td>high</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US04</td>
<td>Search Public Users</td>
<td>high</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US05</td>
<td>See About Us</td>
<td>medium</td>
<td>M02</td>
<td>

Lourenço Gonçalves, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US06</td>
<td>Consult FAQ / Help / Contacts</td>
<td>medium</td>
<td>M02</td>
<td>

Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US07</td>
<td>See Main Features</td>
<td>medium</td>
<td>M02</td>
<td>

**Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US08</td>
<td>Sign-up</td>
<td>high</td>
<td>M01</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US09</td>
<td>Sign-in</td>
<td>high</td>
<td>M01</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US10</td>
<td>Sign-out</td>
<td>high</td>
<td>M01</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US11</td>
<td>View User Profiles</td>
<td>high</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US12</td>
<td>View personalized timeline</td>
<td>high</td>
<td>M02</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US13</td>
<td>Create Post</td>
<td>high</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US14</td>
<td>

View Own

Profile
</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US15</td>
<td>Support Profile Picture</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US16</td>
<td>Recover Password</td>
<td>medium</td>
<td>M01</td>
<td>

**Fábio Sá**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US17</td>
<td>Delete Account</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US18</td>
<td>Send Follow Request</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US19</td>
<td>View profiles followed</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US20</td>
<td>Search for Posts, Comments, Groups and Users</td>
<td>medium</td>
<td>M04</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US21</td>
<td>Exact Match Search</td>
<td>medium</td>
<td>M04</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US22</td>
<td>Full-Text Search</td>
<td>medium</td>
<td>M04</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US23</td>
<td>Search filters</td>
<td>medium</td>
<td>M04</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US24</td>
<td>Search over Multiple Attributes</td>
<td>medium</td>
<td>M04</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US25</td>
<td>Follow someone</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US26</td>
<td>Manage Received Follow Requests</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US27</td>
<td>Manage Followers</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US28</td>
<td>Comment on Posts</td>
<td>medium</td>
<td>M06</td>
<td>

Fábio Sá, **Marcos Ferreira**
</td>
<td>100%</td>
</tr>
<tr>
<td>US29</td>
<td>React to post</td>
<td>medium</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US30</td>
<td>React to comment</td>
<td>medium</td>
<td>M05</td>
<td>

**Fábio Sá**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US31</td>
<td>Create Groups</td>
<td>medium</td>
<td>M07</td>
<td>

**Lourenço Gonçalves**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US32</td>
<td>Manage Group Invitations</td>
<td>medium</td>
<td>M07</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US33</td>
<td>Edit profile</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US34</td>
<td>View Personal notifications</td>
<td>medium</td>
<td>M02</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US35</td>
<td>Placeholders in Form Inputs</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US36</td>
<td>Contextual Error Messages</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US37</td>
<td>Contextual Help</td>
<td>medium</td>
<td>M02</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US38</td>
<td>Follow Requests Notification</td>
<td>medium</td>
<td>M02</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US39</td>
<td>Started Following Notification</td>
<td>low</td>
<td>M02</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US40</td>
<td>Accepted Follow Notification</td>
<td>low</td>
<td>M02</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US41</td>
<td>Invite Group Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US42</td>
<td>Reply to Comments</td>
<td>low</td>
<td>M06</td>
<td>

Marcos Ferreira, **Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US43</td>
<td>Tag Users in Posts</td>
<td>low</td>
<td>M03</td>
<td>

**Marcos Ferreira**, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US44</td>
<td>Post Tagging Notification</td>
<td>low</td>
<td>M03</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US45</td>
<td>Comment and Reply Tagging Notification</td>
<td>low</td>
<td>M06</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US46</td>
<td>Join Public Group</td>
<td>low</td>
<td>M07</td>
<td>

**Lourenço Gonçalves**, André Costa, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US47</td>
<td>Request to Join Private Group</td>
<td>low</td>
<td>M07</td>
<td>

Lourenço Gonçalves, **André Costa**, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US48</td>
<td>Manage my notifications</td>
<td>low</td>
<td>M02</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US49</td>
<td>Delete notifications</td>
<td>low</td>
<td>M02</td>
<td>

Fábio Sá, **Marcos Ferreira**
</td>
<td>100%</td>
</tr>
<tr>
<td>US50</td>
<td>Hashtags</td>
<td>low</td>
<td>M02</td>
<td>

**Marcos Ferreira**, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US51</td>
<td>Notification context</td>
<td>low</td>
<td>M02</td>
<td>

Marcos Ferreira, **Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US52</td>
<td>Send Private Messages</td>
<td>low</td>
<td>M08</td>
<td>

**Marcos Ferreira**, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US53</td>
<td>Private Messages Status</td>
<td>low</td>
<td>M08</td>
<td>

**Marcos Ferreira**, Fábio Sá
</td>
<td>100%</td>
</tr>
<tr>
<td>US54</td>
<td>Send Private Media</td>
<td>low</td>
<td>M08</td>
<td>

**Marcos Ferreira**
</td>
<td>100%</td>
</tr>
<tr>
<td>US55</td>
<td>Edit Group Information</td>
<td>medium</td>
<td>M07</td>
<td>

**Lourenço Gonçalves**, André Costa, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US56</td>
<td>Remove member</td>
<td>medium</td>
<td>M07</td>
<td>

Lourenço Gonçalves, **André Costa**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US57</td>
<td>Add to group</td>
<td>medium</td>
<td>M07</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US58</td>
<td>Remove post from group</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US59</td>
<td>Change group visibility</td>
<td>low</td>
<td>M07</td>
<td>

Lourenço Gonçalves, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US60</td>
<td>Manage Group invitations</td>
<td>low</td>
<td>M07</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US61</td>
<td>Manage Join Requests</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US62</td>
<td>Delete my group</td>
<td>low</td>
<td>M07</td>
<td>

Lourenço Gonçalves, André Costa, **Marcos Ferreira**
</td>
<td>100%</td>
</tr>
<tr>
<td>US63</td>
<td>Give Ownership</td>
<td>low</td>
<td>M07</td>
<td>

Lourenço Gonçalves, A**ndré Costa**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US64</td>
<td>Request Join Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US65</td>
<td>Joined Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US66</td>
<td>Accepted Invite Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US67</td>
<td>Leave Group Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US68</td>
<td>View group members</td>
<td>medium</td>
<td>M07</td>
<td>

**Lourenço Gonçalves**, André Costa, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US69</td>
<td>Post on group</td>
<td>medium</td>
<td>M07</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US70</td>
<td>Leave group</td>
<td>medium</td>
<td>M07</td>
<td>

Lourenço Gonçalves, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US71</td>
<td>Favorite</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US72</td>
<td>Ban Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US73</td>
<td>Ownership Notification</td>
<td>low</td>
<td>M07</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US74</td>
<td>Edit post</td>
<td>high</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US75</td>
<td>Delete post</td>
<td>high</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US76</td>
<td>Likes on Own Post Notification</td>
<td>medium</td>
<td>M03</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US77</td>
<td>Comments on Own Posts Notification</td>
<td>medium</td>
<td>M06</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US78</td>
<td>Manage post visibility</td>
<td>low</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US79</td>
<td>Post media</td>
<td>low</td>
<td>M03</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US80</td>
<td>Edit comment</td>
<td>medium</td>
<td>M06</td>
<td>

**Fábio Sá**, Marcos Ferreira
</td>
<td>100%</td>
</tr>
<tr>
<td>US81</td>
<td>Delete comment</td>
<td>medium</td>
<td>M06</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US82</td>
<td>Likes on Own Comment Notification</td>
<td>low</td>
<td>M06</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US83</td>
<td>Replies on Own Comment Notification</td>
<td>low</td>
<td>M06</td>
<td>

**Fábio Sá**
</td>
<td>100%</td>
</tr>
<tr>
<td>US84</td>
<td>Special Search privileges</td>
<td>high</td>
<td>M05</td>
<td>

**Fábio Sá**, Lourenço Gonçalves, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US85</td>
<td>Administrator Account</td>
<td>medium</td>
<td>M05</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US86</td>
<td>Administer user accounts</td>
<td>medium</td>
<td>M05</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US87</td>
<td>Block and unblock user accounts</td>
<td>medium</td>
<td>M05</td>
<td>

Fábio Sá, Lourenço Gonçalves, **Marcos Ferreira**, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US88</td>
<td>Delete user account</td>
<td>medium</td>
<td>M05</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
<tr>
<td>US89</td>
<td>Delete posts and comments</td>
<td>medium</td>
<td>M05</td>
<td>

Fábio Sá, Lourenço Gonçalves, Marcos Ferreira, **André Costa**
</td>
<td>100%</td>
</tr>
<tr>
<td>US90</td>
<td>Delete groups</td>
<td>low</td>
<td>M05</td>
<td>

Fábio Sá, **Lourenço Gonçalves**, Marcos Ferreira, André Costa
</td>
<td>100%</td>
</tr>
</table>

Table 99: OnlyFEUP implemented features

## A10: Presentation

### Product Presentation

OnlyFEUP is the first FEUP centered social network! With the purpose of facilitating students and teachers to share and discuss ideas and resources about subjects by posting and/or commenting, bringing closeness between them by creating connection and ease of communication. With OnlyFEUP you can create groups to gather people with the same interests, send private messages and much more. Even if you are more conscious about your privacy, there are options to make a private profile, a private group and private posts. 

OnlyFEUP is made with HTML5, JavaScript, CSS. The Bootstrap framework was used to improve the user interface and the Laravel framework, which uses PHP, was used for the back-end and content of the pages. The platform also features an adaptive design and a simple navigation system with relevant information always on the same place, the sidebar. In there you can find the most important actions of a particular page. But even if you have questions, just check the static page. 

URL to the product: https://lbaw2255.lbaw.fe.up.pt

### Video Presentation

![video](uploads/98d6ba8b4cb1bf5e6d50656799a8c6b4/lkj.PNG) https://git.fe.up.pt/lbaw/lbaw2223/lbaw2255/-/blob/main/docs/lbaw2255.mp4

# Revision History

## Artifact A9 - Editor: Fábio Sá

> **November 30:** \
> Delete post with ajax request, https version, fixed post query in feed.\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 1:**\
> Delete account, work on static pages\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 2:**\
> Delete account modification to use transactions\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 3:**\
> Started working on unfollow and following with request (for private accounts), work on notification and new features for groups and groups’ page, work on password recovery\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 4:**\
> Likes and unliking (remove likes), sidebar update, added edit group page\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 5:**\
> Bug fixes in notification, follows and unfollows and deleting a user\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 6:**\
> Notification with ajax request and real time notifications\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 7:**\
> Edit group action, buttons to join group (including request in private groups), admin features for >groups\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 9:**\
> Like’s restrictions, modification in queries for various information.\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 10:**\
> Group notifications, optimizations on search.\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 11:**\
> Comments and subcomments\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 12:**\
> Added feature to remove member from group, likes and unliking for comments\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 13:**\
> Edit comment; counter for comments, subcomments and replies \
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 14:**\
> Groups’ invites, deleting a group, bootstrap setup, started work on group ownership transfer, css changes >and adaptation with bootstrap.\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 15:**\
> Posting in groups, finished group ownership transfer, counter for notifications, bootstrap changes\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 16:**\
> Line breaks in textfields, relative timestamps, fixed create post to not accept empty posts\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 17:**\
> Notifications’ settings, chat section created, css changes, added more media formats\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 18:**\
> Major visual redesign of onlyFEUP (posts, comments, buttons, profile page)\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 19:**\
> Continuation of visual design changes (favicon, sidebar adaptive size, login and register pages, scroll bars, search page)\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 20:**\
> Design changes (edit group page, profile edit page, work on tagging users\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 21:**\
> Comments are now forms, counter now work correctly, bug fixes\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 22:**\
> Addition of chat system\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 23:**\
> Messages in chat now in real time (not reloading needed), added confirmation for important actions, chat design changes, searching comments now fixed\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 24:**\
> Support for images, videos and audio in chat messages\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 27:**\
> Static pages, design changes\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 29:**\
> Added a features page, fixes in general\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **December 30:**\
> Html and css validation (and changes if necessary), update in open api documentation\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

> **January 1:**\
> Fixes in general, database now in contextual to FEUP\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto

## Artifact A10 - Editor: Marcos Ferreira

> **January 2:**\
> Product Presentation text, Video presentation, update wiki\
> **by:** \
> André Costa \
> Fábio Sá \
> Lourenço Gonçalves \
> Marcos Ferreira Pinto