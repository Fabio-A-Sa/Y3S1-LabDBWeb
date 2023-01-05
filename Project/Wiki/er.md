# ER: Requirements Specification Component

> * **Project vision:** \
>   The main objectives of OnlyFeup project is to facilitate students and teachers to share and discuss some ideas and resources about some subjects by posting or comment on someone’s post, bringing a closeness between them by creating connections (friend requests) and ease the creation of groups among co-workers or friends (just to chat).

## A1: OnlyFEUP

> * **Goals, business context and environment.**
>
> > The main goal of the OnlyFEUP project is the development of a web-based social network with the purpose of creating connections between students and staff, sharing resources about courses and subjects. This is a tool that can be used by anyone from FEUP. After signing up and verifying the user is related to the university (students/teachers), they can start using it for a better experience at FEUP.
> >
> > A team of administrators is defined, which will be responsible for managing the system, ensuring it runs smoothly, removing illegal content and material in which they are not the author or have permission to share.
> >
> > This application allows users to integrate into groups and follow students/teachers whom they find their work interesting, they can also create groups if none was found. Users will be able to more easily share resources with people who are actually interested (their followers).
> >
> > Users are separated into groups with different permissions. These groups include the above-mentioned administrators, with access and modification privileges, student users and teacher (FEUP staff) users.
> >
> > The platform will have an adaptive design, allowing users to have a pleasant browsing experience. The product will also provide easy navigation and an excellent overall user experience.
>
> * **Project Stakeholders:**
>
> > * FEUP students
> > * FEUP professors
>
> * **Motivation.**
>
> > To facilitate the lives of people related to feup by showing relevant information by allowing them to follow and block content, instead of the actual model of e-mail, which are sent to everyone (even people completely unrelated to the matter). Follow that to allow users to more easily connect with/find students, teachers, groups they are part in, etc.

> ### Main features
>
> * **User:**
>
> > * **View public timeline**
> > * **View public profiles**
> > * **Login/Logout**
> > * **Registration**
> > * **Recover password**
> > * Delete account
>
> * **Authenticated User:**
>
> > * **View profile**
> > * **Edit profile**
> > * **Suport profile picture**
> > * **View personal notifications**
> > * **View personalized timeline**
> > * **Exact match search**
> > * **Search for public Users**
> > * **Search filters**
> > * **Full-text search**
> > * **Send Friend Requests**
> > * **View profiles followed**
> > * Search for posts, comments, groups and users
> > * **Follow someone**
> > * Manage received follow requests
> > * Manage followers
> > * **Create post**
> > * **Comment on posts**
> > * **Like posts**
> > * Reply to comments
> > * **Create groups**
> > * **View users' feed**
> > * **Join public group**
> > * **Manage notifications**
> > * Tag friends in posts
> > * **Request to join public groups**
>
> * **Post Author:**
>
> > * **Edit post**
> > * **Delete post**
> > * Manage post visibility
>
> * **Comment Author**
>
> > * Edit comment
> > * Delete comment
>
> * **Group Member**
>
> > * View group members
> > * **Post on group**
> > * **Leave group**
>
> * **Group Owner**
>
> > * **Edit group information**
> > * **Remove member**
> > * **Invite to group**
> > * Remove post from group
> > * Change group privacy
> > * Manage group invitations
> > * **Manage join requests**
>
> * **Notifications:**
>
> > * Likes on own post
> > * Comments on own post
> > * Follow request
>
> * **Help:**
> > * **Placeholders in form inputs**
> > * **Contextual error messages**
> > * Contextual help
> > * **About us/Contacts**
> > * Main features
> > * **Help**
>

## A2: Actors and User stories

> Actors and user stories contain specifications about the people that are going to use OnlyFeup in any way and how they are going to use it. It serves as a simple and fast documentation to the projectś requirements.

### 2.1 Actors

> For the OnlyFEUP system, the actors are represented in Figure 1 and described in Table 1.

![Actors](uploads/6108891305efc925cb2941c733041454/Actors.png)

Figure 1: OnlyFEUP actors
| Identifier | Description |
|------------|-------------|
| User | Generic user that has access to public information |
| Visitor | Unauthenticated user that can register itself (sign-up) or sign-in in the system and view the public feed |
| Authenticated | Authenticated users that can consult information, insert works, items and ideas, comment on another person’s work and manage and chat in your groups. |
| Group Owner | Users that created or have permissions to edit the group name, description, group’s visibility, add/remove participants, edit participants permissions (member to owner or vice-versa). |
| Group Member | Users that can participate, chat and socialize in a community/group but don't have the permissions of an owner |
| Post Author | Users that can edit or delete their own post |
| Comment Author | Users that can comment on someone’s or on their own posts |
| Administrator | Administrator have the power to remove posts (to remove offensive  posts) and block/unblock people |

Table 1: OnlyFEUP actors description

### 2.2 User Stories

> For the OnlyFEUP system, consider the user stories that are presented in the following sections.

#### 2.2.1 User

<table>
<tr>
<td>

**Identifier**
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
<td>As a User, I want to be able to view the Public Timeline so that I can keep up with what’s happening.</td>
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
<td>US05</td>
<td>See About Us</td>
<td>medium</td>
<td>As a User, I want an ‘About Us’ section so that I can find out more about OnlyFEUP’s creators.</td>
</tr>
<tr>
<td>US06</td>
<td>Consult FAQ / Help / Contacts</td>
<td>medium</td>
<td>As a User, I want a ‘FAQ / Help’ section so that I can find a solution to a problem</td>
</tr>
<tr>
<td>US07</td>
<td>See Main Features</td>
<td>medium</td>
<td>As a User, I want a ‘Main Features’ section, so that I can find what can I expect from OnlyFEUP</td>
</tr>
</table>


Table 2: User user stories

#### 2.2.2 Visitor

<table>
<tr>
<td>

**Identifier**
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
<td>US08</td>
<td>Sign-up</td>
<td>high</td>
<td>

As a Visitor, I want to register myself into the system, so that I can authenticate myself into the system
</td>
</tr>
<tr>
<td>US09</td>
<td>Sign-in</td>
<td>high</td>
<td>

As a Visitor, I want to authenticate into the system, so that I can access privileged information
</td>
</tr>
</table>

Table 3: Visitor user stories

#### 2.2.3 Authenticated User

<table>
<tr>
<td>

**Identifier**
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
<td>US10</td>
<td>Sign-out</td>
<td>high</td>
<td>
As an Authenticated User, I want to sign out, so that I am not logged in anymore.
</td>
</tr>
<tr>
<td>US11</td>
<td>View User Profiles</td>
<td>high</td>
<td>
As an Authenticated User, I want to see User Profiles, so that I can see their posts
</td>
</tr>
<tr>
<td>US12</td>
<td>View personalized timeline</td>
<td>high</td>
<td>
As an Authenticated User, I want to view a personalized timeline, so that I can view posts that I actually have an interest in.
</td>
</tr>
<tr>
<td>US13</td>
<td>Create Post</td>
<td>high</td>
<td>As an Authenticated User, I want to create posts, so that I can share information to my followers.</td>
</tr>
<tr>
<td>US14</td>
<td>View Own Profile
</td>
<td>medium</td>
<td>
As an Authenticated User, I want to see my profile, so that I can see what I wrote in the past
</td>
</tr>
<tr>
<td>US15</td>
<td>Support Profile Picture</td>
<td>medium</td>
<td>
As an Authenticated User, I want to see my profile picture, so that I can share my face.
</td>
</tr>
<tr>
<td>US16</td>
<td>Recover Password</td>
<td>medium</td>
<td>
As an Authenticated User, I want to recover my password, so that Change it in case I forget it.
</td>
</tr>
<tr>
<td>US17</td>
<td>Delete Account</td>
<td>medium</td>
<td>
As an Authenticated User, I want to delete my account, so that I can stop thinking about FEUP.
</td>
</tr>
<tr>
<td>US18</td>
<td>Send Follow Request</td>
<td>medium</td>
<td>
As an Authenticated User, I want to send follow request to a private profile, so that I can show interest in following the respective user.
</td>
</tr>
<tr>
<td>US19</td>
<td>View profiles followed</td>
<td>medium</td>
<td>
As an Authenticated User, I want to view profiles followed, so that I can get information and view posts about people that I follow.
</td>
</tr>
<tr>
<td>US20</td>
<td>Search for Posts, Comments, Groups and Users</td>
<td>medium</td>
<td>
As an Authenticated User, I want to search for stuff, so that I can find things relevant for me at a certain time.
</td>
</tr>
<tr>
<td>US21</td>
<td>Exact Match Search</td>
<td>medium</td>
<td>
As an Authenticated User, I want to search exactly what I am looking for, so that I can find things relevant for me at a certain time.
</td>
</tr>
<tr>
<td>US22</td>
<td>Full-Text Search</td>
<td>medium</td>
<td>
As an Authenticated User, I want to search in Posts and Comments texts, so that I can find things relevant for me.
</td>
</tr>
<tr>
<td>US23</td>
<td>Search filters</td>
<td>medium</td>
<td>
As a user, I want to be able to filter the search results so that I can find what I want more efficiently
</td>
</tr>
<tr>
<td>US24</td>
<td>Search over Multiple Attributes</td>
<td>medium</td>
<td>
As a user, I want to be able to search over text attributes, like name or username, description or title, so that I can find relevant data more efficiently.
</td>
</tr>
<tr>
<td>US25</td>
<td>Follow someone</td>
<td>medium</td>
<td>
As an Authenticated User, I want to follow someone with a public profile, so that I can start viewing their posts in my feed.
</td>
</tr>
<tr>
<td>US26</td>
<td>Manage Received Follow Requests</td>
<td>medium</td>
<td>
As an Authenticated User, I want to manage received follow requests, so that I can control who can follow me or not.
</td>
</tr>
<tr>
<td>US27</td>
<td>Manage Followers</td>
<td>medium</td>
<td>
As an Authenticated User, I want to manage followers, so that I can remove someone from following me.
</td>
</tr>
<tr>
<td>US28</td>
<td>Comment on Posts</td>
<td>medium</td>
<td>
As an Authenticated User, I want to comment on posts, so that I can share my point of view or pass information related to the post.
</td>
</tr>
<tr>
<td>US29</td>
<td>React to post</td>
<td>medium</td>
<td>
As an Authenticated User, I want to like posts, so that I can show my support of it.
</td>
</tr>
<tr>
<td>US30</td>
<td>React to comment</td>
<td>medium</td>
<td>
As an Authenticated User, I want to like comments, so that I can show my support of it.
</td>
</tr>
<tr>
<td>US31</td>
<td>Create Groups</td>
<td>medium</td>
<td>
As an Authenticated User, I want to create a group, so that I can share information about specific topics to people who are interested in it.
</td>
</tr>
<tr>
<td>US32</td>
<td>Manage Group Invitations</td>
<td>medium</td>
<td>
As an Authenticated User, I want to manage group invitations, so that I can control the amount of invitations that I receive about a certain topic.
</td>
</tr>
<tr>
<td>US33</td>
<td>Edit profile</td>
<td>medium</td>
<td>
As an Authenticated User, I want to edit my profile, so that I can change my picture, my name and/or my description.
</td>
</tr>
<tr>
<td>US34</td>
<td>View Personal notifications</td>
<td>medium</td>
<td>
As an Authenticated User, I want to view notifications, so that I can view who interacted (or wants to interact) with me.
</td>
</tr>
<tr>
<td>US35</td>
<td>Placeholders in Form Inputs</td>
<td>medium</td>
<td>
As an Authenticated User, I want to see placeholders in form inputs, so I can have help on what to write there.
</td>
</tr>
<tr>
<td>US36</td>
<td>Contextual Error Messages</td>
<td>medium</td>
<td>
As an Authenticated User, I want to see contextual error messages after inputs, so I can have help on what went wrong.
</td>
</tr>
<tr>
<td>US37</td>
<td>Contextual Help</td>
<td>medium</td>
<td>
As an Authenticated User, I want to see contextual help, so I can have help on how to perform any task.
</td>
</tr>
<tr>
<td>US38</td>
<td>Follow Requests Notification</td>
<td>medium</td>
<td>
As an Authenticated User, I want to receive notification when someone requests to follow me, so I keep informed about possible followers.
</td>
</tr>
<tr>
<td>US39</td>
<td>Started Following Notification</td>
<td>low</td>
<td>
As an Authenticated User, I want to receive notification when someone starts to follow me, so I keep informed about new followers.
</td>
</tr>
<tr>
<td>US40</td>
<td>Accepted Follow Notification</td>
<td>low</td>
<td>
As an Authenticated User, I want to receive notification when someone accepts my follow request, so I keep informed about users that I've been following recently
</td>
</tr>
<tr>
<td>US41</td>
<td>Invite Group Notification</td>
<td>low</td>
<td>
As an Authenticated User, I want to receive notification when someone invites me to join their group, so I can accept or not the invitation.
</td>
</tr>
<tr>
<td>US42</td>
<td>Reply to Comments</td>
<td>low</td>
<td>
As an Authenticated User, I want to reply to comments, so that I can share my point of view or pass information related to a comment.
</td>
</tr>
<tr>
<td>US43</td>
<td>Tag Users in Posts</td>
<td>low</td>
<td>
As an Authenticated User, I want to be able to tag users in my posts so that I can inform the viewers of the people related to the posts and their respective accounts.
</td>
</tr>
<tr>
<td>US44</td>
<td>Post Tagging Notification</td>
<td>low</td>
<td>
As an Authenticated User, I want to receive notification when someone tags me in their post, so I keep informed.
</td>
</tr>
<tr>
<td>US45</td>
<td>Comment and Reply Tagging Notification</td>
<td>low</td>
<td>
As an Authenticated User, I want to receive notification when someone tagged me in their comment or reply, so I keep informed.
</td>
</tr>
<tr>
<td>US46</td>
<td>Join Public Group</td>
<td>low</td>
<td>
As an Authenticated User, I want to be able to join public groups so that I can view its posts and interact with its members.
</td>
</tr>
<tr>
<td>US47</td>
<td>Request to Join Private Group</td>
<td>low</td>
<td>
As an Authenticated User, I want to able to request join private groups, so that I can view its posts and interact with its members
</td>
</tr>
<tr>
<td>US48</td>
<td>Manage my notifications</td>
<td>low</td>
<td>
As an Authenticated User, I want to be able to manage what notifications I receive, so that I am only notified for things I care about.
</td>
</tr>
<tr>
<td>US49</td>
<td>Delete notifications</td>
<td>low</td>
<td>
As an Authenticated User, I want to delete old notifications, so that I clear my notifications page.
</td>
</tr>
<tr>
<td>US50</td>
<td>Hashtags</td>
<td>low</td>
<td>
As an Authenticated User, I want to be able to create, see and search for hashtags in posts, comments or descriptions
</td>
</tr>
<tr>
<td>US51</td>
<td>Notification context</td>
<td>low</td>
<td>
As an Authenticated User, I want to see notification context, so that I can see post/comment/subcomment associated it
</td>
</tr>
<tr>
<td>US52</td>
<td>Send Private Messages</td>
<td>low</td>
<td>
As an Authenticated User, I want to send private messages to another OnlyFEUP user, so I can contact them directly.
</td>
</tr>
<tr>
<td>US53</td>
<td>Private Messages Status</td>
<td>low</td>
<td>
As an authenticated user, I want to see if there are any new unseen private messages so I can quickly see who has contacted me recently
</td>
</tr>
<tr>
<td>US54</td>
<td>Send Private Media</td>
<td>low</td>
<td>
As an Authenticated User, I want to send media (photos, videos or audios) to another OnlyFEUP user, so I can contact them directly and in private mode.
</td>
</tr>
</table>

Table 4: Authenticated User user stories

#### 2.2.4 Group Owner

<table>
<tr>
<td>

**Identifier**
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
<td>US55</td>
<td>Edit Group Information</td>
<td>medium</td>
<td>
As a Group Owner, I want to edit the name or the description of the group, so that the purpose of the group is well explained.
</td>
</tr>
<tr>
<td>US56</td>
<td>Remove member</td>
<td>medium</td>
<td>
As a Group Owner, I want to remove/ban a member of a group, so that they can no longer access the information of the group.
</td>
</tr>
<tr>
<td>US57</td>
<td>Add to group</td>
<td>medium</td>
<td>
As a Group Owner, I want to add someone to the group, so that they can access the group information.
</td>
</tr>
<tr>
<td>US58</td>
<td>Remove post from group</td>
<td>low</td>
<td>
As a Group Owner, I want to remove a post from a group, so that its information is no longer visible.
</td>
</tr>
<tr>
<td>US59</td>
<td>Change group privacy</td>
<td>low</td>
<td>
As a Group Owner, I want to change the group privacy, so that I can choose if the group information is public or not and who can enter in the group.
</td>
</tr>
<tr>
<td>US60</td>
<td>Manage Group invitations</td>
<td>low</td>
<td>
As a Group Owner, I want to manage my invitations, so that I can delete an invitation if I change my mind.
</td>
</tr>
<tr>
<td>US61</td>
<td>Manage Join Requests</td>
<td>low</td>
<td>
As a Group Owner, I want to manage group join requests, so that I can accept or reject possible members.
</td>
</tr>
<tr>
<td>US62</td>
<td>Delete my group</td>
<td>low</td>
<td>
As a Group Owner, I want to delete my group so all members can no longer access the information of the group
</td>
</tr>
<tr>
<td>US63</td>
<td>Give Ownership</td>
<td>low</td>
<td>
As a Group Owner, I want to give ownership of my group, so that I will be not group owner anymore
</td>
</tr>
<tr>
<td>US64</td>
<td>Request Join Notification</td>
<td>low</td>
<td>
As a Group Owner, I want to receive request join notifications, so that I see who wants to join in my group.
</td>
</tr>
<tr>
<td>US65</td>
<td>Joined Notification</td>
<td>low</td>
<td>
As a Group Owner, I want to receive join notifications, so that I see who is joining my group.
</td>
</tr>
<tr>
<td>US66</td>
<td>Accepted Invite Notification</td>
<td>low</td>
<td>
As a Group Owner, I want to receive accepted invite notifications, so that I see who accepts my invitation to join my group.
</td>
</tr>
<tr>
<td>US67</td>
<td>Leave Group Notification</td>
<td>low</td>
<td>
As a Group Owner, I want to receive leave group notifications, so that I see who leaves my group.
</td>
</tr>
</table>

Table 5: Group owner user stories

#### 2.2.5 Group Member

<table>
<tr>
<td>

**Identifier**
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
<td>US68</td>
<td>View group members</td>
<td>medium</td>
<td>
As a Group Member, I want to see the members of a group, so that I can see users with the same likes as me
</td>
</tr>
<tr>
<td>US69</td>
<td>Post on group</td>
<td>medium</td>
<td>
As a Group Member, I want to post on a group, so that all members can see or access the idea/resource that I shared.
</td>
</tr>
<tr>
<td>US70</td>
<td>Leave group</td>
<td>medium</td>
<td>
As a Group Member, I want to be able to leave a group, so that I no longer receive notifications from it.
</td>
</tr>
<tr>
<td>US71</td>
<td>Favorite</td>
<td>low</td>
<td>
As a Group Member, I want to be able to mark groups as my favorites 
</td>
</tr>
<tr>
<td>US72</td>
<td>Ban Notification</td>
<td>low</td>
<td>
As a Group Member, I want to receive notification when I get banned from a group, so I keep informed.
</td>
</tr>
<tr>
<td>US73</td>
<td>Ownership Notification</td>
<td>low</td>
<td>
As a Group Member, I want to receive a notification when I become the group owner, so I can stay informed about my new group.
</td>
</tr>
</table>

Table 6: Group member user stories

#### 2.2.6 Post Author

<table>
<tr>
<td>

**Identifier**
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
<td>US74</td>
<td>Edit post</td>
<td>high</td>
<td>
As a Post Author, I want to edit my post, so that I can correct a mistake I made.
</td>
</tr>
<tr>
<td>US75</td>
<td>Delete post</td>
<td>high</td>
<td>
As a Post Author, I want to delete a post, so that I can remove something that I put by mistake.
</td>
</tr>
<tr>
<td>US76</td>
<td>Likes on Own Post Notification</td>
<td>medium</td>
<td>
As a Post Author, I want to receive notification when someone likes my post, so I stay informed.
</td>
</tr>
<tr>
<td>US77</td>
<td>Comments on Own Posts Notification</td>
<td>medium</td>
<td>
As a Post Author, I want to receive notification when someone comments on my post, so I keep informed of the user's opinions.
</td>
</tr>
<tr>
<td>US78</td>
<td>Manage post visibility</td>
<td>low</td>
<td>
As a Post Author, I want to manage post visibility, so that I can restrict the post visibility only to my followers.
</td>
</tr>
<tr>
<td>US79</td>
<td>Post media</td>
<td>low</td>
<td>
As a Post Author, I want to see my posts with media (photos, videos), so I can share them instead of text.
</td>
</tr>
</table>

Table 7: Post Author user stories

### 2.2.7 Comment Author

<table>
<tr>
<td>

**Identifier**
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
<td>US80</td>
<td>Edit comment</td>
<td>medium</td>
<td>
As a Comment Author, I want to edit my comment, so that I can correct a mistake I made.
</td>
</tr>
<tr>
<td>US81</td>
<td>Delete comment</td>
<td>medium</td>
<td>
As a Comment Author, I want to delete my comment, so that I can remove a comment that is no longer relevant.
</td>
</tr>
<tr>
<td>US82</td>
<td>Likes on Own Comment Notification</td>
<td>low</td>
<td>
As a Comment Author, I want to receive notification when someone likes my comment, so I stay informed.
</td>
</tr>
<tr>
<td>US83</td>
<td>Replies on Own Comment Notification</td>
<td>low</td>
<td>
As a Comment Author, I want to receive notification when someone replies to my comment, so I keep informed of the user's opinions.
</td>
</tr>
</table>

Table 8: Comment Author user stories

#### 2.2.8 Administrator

<table>
<tr>
<td>

**Identifier**
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
<td>US84</td>
<td>Special Search privileges</td>
<td>high</td>
<td>
As an Administrator, I want to search for profiles and groups even if they are private, so that I can investigate if there is something against the guidelines.
</td>
</tr>
<tr>
<td>US85</td>
<td>Administrator Account</td>
<td>medium</td>
<td>
As an Administrator, I want to have an administrator account, so I can see OnlyFEUP data with privileges.
</td>
</tr>
<tr>
<td>US86</td>
<td>Administer user accounts</td>
<td>medium</td>
<td>
As an Administrator, I want to administer user accounts, so that I can investigate a report.
</td>
</tr>
<tr>
<td>US87</td>
<td>Block and unblock user accounts</td>
<td>medium</td>
<td>
As an Administrator, I want to block an user, so that I can punish them for not following the guidelines.
</td>
</tr>
<tr>
<td>US88</td>
<td>Delete user account</td>
<td>medium</td>
<td>
As an Administrator, I want to delete an user account, so that I can remove an user that repeatedly broke the guidelines.
</td>
</tr>
<tr>
<td>US89</td>
<td>Delete posts and comments</td>
<td>medium</td>
<td>
As an Administrator, I want to delete posts and comments, so that I can remove content that does not follow the guidelines.
</td>
</tr>
<tr>
<td>US90</td>
<td>Delete groups</td>
<td>low</td>
<td>
As an Administrator, I want to delete groups, so that I can remove content that does not follow the guidelines.
</td>
</tr>
</table>

Table 9: Administrator user stories

## 2.3 Supplementary Requirements

### 2.3.1 Business rules

<table>
<tr>
<td>

**Identifier**
</td>
<td>

**Name**
</td>
<td>

**Description**
</td>
</tr>
<tr>
<td>BR01</td>
<td>Deleted account</td>
<td>
The history of an item must be maintained even if the item is deleted in order not to lose the loan record for all items.
</td>
</tr>
<tr>
<td>BR02</td>
<td>Only people related to Feup</td>
<td>
Since it will only work when connected to the university’s wifi or VPN, it is already restricted to people related to FEUP.
</td>
</tr>
<tr>
<td>BR03</td>
<td>Media types</td>
<td>

Media types needs to be as required because of the size of files itself:

* Images in jpg,png,jpeg,gif
* Videos in mp4
* Audios in mp3

Since they are more compact (although not lossless) and will be less taxing on the database.</td>
</tr>
<tr>
<td>BR04</td>
<td>Profiles</td>
<td>
Profiles can be public or private. Content of private profiles are only visible to followers or administrators.
</td>
</tr>
<tr>
<td>BR05</td>
<td>Interact with own Posts</td>
<td>
Authenticated users can comment/like their own posts/comments.
</td>
</tr>
<tr>
<td>BR06</td>
<td>Dates</td>
<td>
The date of each post is always less than or equal to the current date.
</td>
</tr>
</table>

Table 10: OnlyFEUP Business Rules

### 2.3.2 Technical requirements

<table>
<tr>
<td>

**Identifier**
</td>
<td>

**Name**
</td>
<td>

**Description**
</td>
</tr>
<tr>
<td>TR01</td>
<td>Availability</td>
<td>The system must be available 99 percent of the time.</td>
</tr>
<tr>
<td>

**TR02**
</td>
<td>

**Accessibility**
</td>
<td>

**The system should work with different hardware and software, so that OnlyFEUP can be available to a plethora of users (even if the hardware/software is reasonably old).**
</td>
</tr>
<tr>
<td>

**TR03**
</td>
<td>

**Usability**
</td>
<td>

**The system should be simple and easy to use.**
</td>
</tr>
<tr>
<td>TR04</td>
<td>Performance</td>
<td>The system should have response times that are reasonably fast and does not handicap the users’ usability of the product.</td>
</tr>
<tr>
<td>TR05</td>
<td>Web application</td>
<td>The system should be implemented HTML, CSS, javascript and PHP</td>
</tr>
<tr>
<td>TR06</td>
<td>Portability</td>
<td>The server-side system should be platform independent and work with different hardware/software to accommodate eventual changes and upgrades deemed necessary.</td>
</tr>
<tr>
<td>TR07</td>
<td>Database</td>
<td>The system should store data in a reliable and non-redundant database.</td>
</tr>
<tr>
<td>

**TR08**
</td>
<td>

**Security**
</td>
<td>

**The system shall protect information from unauthorized access, ensuring that only the user itself, the people whom the user has shared the information and administration of onlyFEUP can access the data, and even so only as a necessity.**
</td>
</tr>
<tr>
<td>TR09</td>
<td>Robustness</td>
<td>The system must be prepared to handle errors and be able to continue working/boot itself up in case of a failure.</td>
</tr>
<tr>
<td>TR10</td>
<td>Scalability</td>
<td>The system must be prepared to deal with the growth in the number of users, and the number of posts and files to be stored.</td>
</tr>
<tr>
<td>TR11</td>
<td>Ethics</td>
<td>The system must respect the legislation of the places where it operates (in our case Portugal), and the system should respect the users’ preferences and privacy.</td>
</tr>
</table>

Table 11: OnlyFEUP technical requirements

Usability and accessibility were chosen as the most critical technical requirements because, since it is a social network, it needs to be simple and easy to use by any user, also because of the many users and their different equipment, software, operating system, ways of using the systems, versions of software, etc it needs to be robust to handle all of the these mentioned above. 

Since the user is also going to be creating and submitting information as normal usage of the system, it is important that their information is secure from unauthorized access and in compliance with the privacy regulations.

#### 2.3.3. Restrictions

<table>
<tr>
<td>

**Identifier**
</td>
<td>

**Name**
</td>
<td>

**Description**
</td>
</tr>
<tr>
<td>R01</td>
<td>Deadline</td>
<td>The system should be ready to be used by the end of the semester</td>
</tr>
<tr>
<td>R02</td>
<td>Database</td>
<td>The database should use PostgreSQL</td>
</tr>
</table>

Table 12: OnlyFEUP restrictions

---

## A3: Information Architecture

### 1. Sitemap

> The image below represents the pages that are going to exist in onlyFEUP and how they can be accessed or used. It is a simple design of the process of using the website.

![Sitemap](uploads/05bb5947e7d35a571130398c5421da7b/Sitemap.png)

Figure 2: OnlyFEUP SiteMap

### 2. Wireframes

> The wireframes below show the template and localization of the important interactive elements. The home page is the most significant page in our system and because of it it is represented below, the next pages that required some clarification and thinking about its usability are the profile page and the groups page. As such they also have wireframes.

#### UI01: Home Page

![HomePage](uploads/5a999baae8e4d40323ba6cc62d193c79/HomePage.png)

Figure 3: OnlyFEUP HomePage

1. Dropdown menu with various options
2. Side-bar for quick access to groups
3. Button to reach the Groups Page
4. Buttons to reach pages with help and information about the developers
5. Zone to interact with Posts; owner of the post have option to edit or delete post
6. Direct access to search feature
7. Notifications and post buttons respectively

#### UI10: Profile Page

![ProfilePage](uploads/74b805b5c53bd3538533ec5768954ee8/ProfilePage.png)

Figure 4: OnlyFEUP ProfilePage

1. Dropdown menu with various options
4. Buttons to reach pages with help and information about the developers
5. Zone to interact with Posts; owner of the post have option to edit or delete post
7. Post button
8. Side-bar with access to edit profile page and the media of the user
9. Button to return to the previous page
10. Access to the Followers and Following pages respectively

#### UI20: Groups Page

![GroupsPage](uploads/77ce073ccd32ef8fdfd6698e00f54944/GroupsPage.png)

Figure 5: OnlyFEUP Groups Page

1. Dropdown menu with various options
2. Side-bar for quick access to groups
4. Buttons to reach pages with help and information about the developers
6. Direct access to search feature
9. Button to return to the previous page
11. Direct access to the “Create new group” page
12. Toggle between “groups I take part in” and “public groups”
13. Direct access to the group where you can define if it is favorite or not

---

## Revision history

### Artifacts: A1

#### Editor: Fábio Sá

> **September 22:** \
>   Context of project, stakeholders, motivation \
> **by:** \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **September 25:** \
>   Main features, groups, relation between groups, goals and objectives \
> **by:** \
>   André Costa \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

### Artifacts: A2

#### Editor: Marcos Ferreira

> **September 25:** \
>   Identifying actors and their descriptions, started user stories; business rules 1,2,3 \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **September 26:** \
>   Started formating tables for user stories \
> **by:** \
>   Fábio Sá \
>   Marcos Ferreira Pinto 

> **September 27:** \
>   Finished user stories, business rules, technical requirements, restrictions. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **October 02:** \
>   Text reformatting and some corrections\
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **October 04:** \
>   Corrections to user stories, business rules and technical requirements \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

### Artifacts: A3

#### Editor: Lourenço Gonçalves

> **September 27:** \
>   Started and finished sitemap. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **September 29:** \
>   Corrections to sitemap. Started working on wireframes \
> **by:** \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **October 02:** \
>   Finished wireframes. \
> **by:** \
>   André Costa \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

> **October 04:** \
>   Sitemap labels. Final corrections to sitemap. \
> **by:** \
>   André Costa \
>   Fábio Sá \
>   Lourenço Gonçalves \
>   Marcos Ferreira Pinto 

---

### Group lbaw2255, 25/09/2022