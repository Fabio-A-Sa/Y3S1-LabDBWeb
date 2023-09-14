function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
  }
  
  function sendAjaxRequest(method, url, data) {
    let request = new XMLHttpRequest();
  
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(encodeForAjax(data));
  }
  
  function getCSRF() {
    let tokenField = document.createElement('input')
    tokenField.type = "hidden"
    tokenField.name = "_token"
    tokenField.value = document.querySelector("head meta[name='csrf-token']").content;
    return tokenField;
  }
  
  function editPost(id) {
  
    let post = document.querySelector("#post" + id);
    let main = post.querySelector("main");
    let content_place = main.querySelector("#main-content");
    let image = main.querySelector("img.postmedia");
    let visibility = main.querySelector("h3.postvisibility");
    let content = main.querySelector("h3.postcontent");
  
    // transformar o content numa caixa de texto
    let textarea = document.createElement('textarea');
    textarea.type = 'textbox';
    textarea.className = 'postcontent'
    textarea.value = (content === null) ? "" : parseContentEdit(content.innerHTML);
    content_place.insertBefore(textarea, content);
  
    // construção de uma checkbox com base no .innerHTML
    let checkbox = document.createElement('input');
    checkbox.checked = visibility.innerHTML == 'public'
    checkbox.type = 'checkbox';
    checkbox.className = 'visibilityCheck';
    let label = document.createElement('label');
    label.innerHTML = 'Public Post?'
    label.className = 'checkLabel';
    label.appendChild(checkbox);
    content_place.insertBefore(label, content);
     if (content !== null) content_place.removeChild(content);
  
    document.querySelector('#cancelEditPost' + id).style.visibility = 'visible';
  
    //change button edit to confirm
    let edit_button = document.querySelector("#editPost" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-pencil");
    edit_button_icon.classList.add("fa-floppy-o");
  
    // mudar o onclick do botão
    let button = document.querySelector('#editPost' + id);
    button.onclick = () => {
        sendEditPost(id);
    }
  
  }
  
  async function sendEditPost(id) {
  
    let post = document.querySelector("#post" + id);
    let main = post.querySelector("main");
    let content_place = main.querySelector("#main-content");
    let image = main.querySelector("img.postmedia");
    let visibility = main.querySelector("input.visibilityCheck");
    let content = main.querySelector("textarea.postcontent");
  
    let newcontent = await parseContentSend(content.value);
    newcontent = newcontent.replace("\n", "<br />");
  
    if (newcontent == '' && image == null) {
      alert("Empty post. Please try again.");
      return;
    }
  
    if (newcontent.length > 255) {
      alert("Post max: 255. Please try again.");
      return;
    }
  
    sendAjaxRequest('post', '../post/edit', {id: id, content: content.value, is_public: visibility.checked})
  
    main.querySelector("h3.postvisibility").innerHTML = visibility.checked ? 'public' : 'private'
    main.querySelector('.checkLabel').remove();
    let h3 = document.createElement('h3');
    h3.innerHTML = newcontent
    h3.className = 'postcontent'
    content_place.insertBefore(h3, content_place.firstChild);
    content.remove();
  
    document.querySelector('#cancelEditPost' + id).style.visibility = 'hidden';
  
    //change button edit to confirm
    let edit_button = document.querySelector("#editPost" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-floppy-o");
    edit_button_icon.classList.add("fa-pencil");
  
    // mudar o onclick do botão
    let button = document.querySelector('#editPost' + id);
    button.onclick = () => {
        editPost(id);
    }
  }
  
  function cancelEditPost() {
    window.location.reload();
  }
  
  function createPost(id) {
    let message = document.querySelector("#alert");
    if (message) message.remove();
    let form = document.querySelector("#createPostModal");
    form.hidden = false;
  
    if(id != null){
      let input = document.createElement("input");
      input.hidden = true;
      input.value = id;
      input.name = "group_id";
      form.querySelector('form').appendChild(input);
    }
  }
  
  function createGroup() {
    let message = document.querySelector("#alert");
    if (message) message.remove();
    let form = document.querySelector("#createPostModal");
    form.hidden = false;
  }
  
  function blockUser(id) {
    let button = document.querySelector("#block" + id)
    if (button.innerHTML == "Unblock") {
      sendAjaxRequest('post', '../admin/user/unblock', {id: id})
      document.querySelector('#block'+id).innerHTML = "Block"
    } else {
      sendAjaxRequest('post', '../admin/user/block', {id: id})
      document.querySelector('#block'+id).innerHTML = "Unblock"
    }
  }
  
  function deletePost(id, type) {
    sendAjaxRequest('post', '../post/delete', {id: id})
    document.querySelector('#post'+id).remove()
    updateStatistics(-1, type);
    updateStatistics(-1, 'postResults');
  }
  
  function deleteMessage() {
    document.querySelector("#alert").remove()
  }
  
  function deleteUser(id, type) { 
    if (type == "profile") {
      document.querySelector('#delete-user-form-button').click();
    } else {
      sendAjaxRequest('post', '../user/delete', {id: id});
      document.querySelector("#user" + id).remove();
      updateStatistics(-1, type);
    }
  }
  
  function getProfileImage(id) {
    let attemp_image = "../images/profile/" + id + ".jpg"
    var request = new XMLHttpRequest();
    request.open('HEAD', attemp_image, false);
    request.send();
    return request.status == "404" ? "../images/profile/default.jpg" : attemp_image;
  }
  
  function updateStatistics(quantity, id) {
    let statistic = document.getElementById(id)
    if (statistic) {
      let n = parseInt(statistic.innerHTML.match(/\d+/g)[0]) + quantity
      statistic.innerHTML = statistic.innerHTML.replace(/\d+/g, n)
    }
  }
  
  function updateStatisticsHTML(quantity, id) {
    let statistic = document.getElementById(id)
    if (statistic) {
      let n = parseInt(statistic.querySelector("h4").innerHTML.match(/\d+/g)[0]) + quantity
      statistic.querySelector("h4").innerHTML = statistic.querySelector("h4").innerHTML.replace(/\d+/g, n)
    }
  }
  
  function showRecoverPassword() {
      document.querySelector("#recover").hidden = false;
      document.querySelector("#recoverButton").remove()
  }
  
  function recoverPassword() {
    const email = document.querySelector("#recoverEmail").value;
    if (email == "") {
      alert("Empty email. Please try again.")
      return;
    }
    document.querySelector("#recoverAttemp").value = email;
    sendAjaxRequest('post', '../sendEmail', {email: email});
    document.querySelector("#recover").hidden = true;
    document.querySelector("#recoverPassword").hidden = false;
  }
  
  function removeFollower(id) {
    sendAjaxRequest('post', 'removeFollower', {id: id});
    updateStatistics(-1, 'userFollowers');
    document.querySelector("#follower" + id).remove();
  }
  
  function removeFollow(id) {
    sendAjaxRequest('post', 'unfollow', {id: id});
    updateStatistics(-1, 'userFollowing')
    document.querySelector("#followed" + id).remove();
  }
  
  function updateState(userID, myID, publicProfile) {
    const state_html = document.querySelector('#state' + userID).innerHTML.replace(/\s/g, '');
    const state = state_html.replace( /(<([^>]+)>)/ig,'');
    console.log(state);
    switch (state) {
      case 'Follow':
        if (publicProfile) {
  
          sendAjaxRequest('post', 'follow', {id: userID});
          document.querySelector('#state' + userID).innerHTML = '<i id="text-icon" class="fa fa-minus-circle" aria-hidden="true"></i> Unfollow';
          updateStatistics(1, 'userFollowers');
  
          let article = document.createElement('article');
          article.className = 'user-admin-result';
          article.id = 'follower' + myID;
          let image = document.createElement('img');
          image.src = getProfileImage(myID);
          image.className = 'user-profile-pic';
          article.appendChild(image);
          let a = document.createElement('a');
          a.href = '../user/' + myID;
          let h3 = document.createElement('h3');
          h3.className = 'user-username'
          h3.innerHTML = document.querySelector('#username' + myID).innerHTML;
          a.appendChild(h3);
          article.appendChild(a);
          let followers = document.querySelector('#followers');
          followers.insertBefore(article, followers.firstChild);
  
        } else {
          sendAjaxRequest('post', 'doFollowRequest', {id: userID});
          document.querySelector('#state' + userID).innerHTML = '<i id="text-icon" class="fa fa-times-circle" aria-hidden="true"></i> Cancel follow request';
        }
        break;
      case 'Unfollow':
        sendAjaxRequest('post', 'unfollow', {id: userID});
        document.querySelector('#state' + userID).innerHTML = '<i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Follow';
        let me = document.querySelector("#follower" + myID)
        if (me) me.remove()
        updateStatistics(-1, 'userFollowers');
        break;
      case 'Cancelfollowrequest':
        sendAjaxRequest('post', 'cancelFollowRequest', {id: userID});
        document.querySelector('#state' + userID).innerHTML = '<i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Follow';
        break;
      default:
        break;
    }
  }
  
  function acceptRequestFollow(userID, notificationID) {
    sendAjaxRequest('post', '../user/acceptFollowRequest', {id: userID})
    let text = document.querySelector('#notification' + notificationID + " > h3")
    text.innerHTML = text.innerHTML.replace("request", "started")
    document.querySelector('.request').remove()
    document.querySelector('.request').remove()
  
    let button = document.createElement('button');
    button.innerHTML = "X"
    button.className = "notification-button";
    button.onclick = () => removeNotification(notificationID);
    document.querySelector('#notification' + notificationID).appendChild(button);
  }
  
  function rejectRequestFollow(userID, notificationID) {
    sendAjaxRequest('post', '../user/rejectFollowRequest', {id: userID})
    document.querySelector('#notification' + notificationID).remove()
  }
  
  function removeNotification(notificationID) {
    sendAjaxRequest('post', '../notification/delete', {id: notificationID})
    document.querySelector('#notification' + notificationID).remove()
  }
  
  function likePost(id) {
    let like = document.querySelector('#countPostLikes' + id);
    let likecounter = like.querySelector('#text-config');
    let numberLikes = parseInt(likecounter.innerHTML.match(/^[^\d]*(\d+)/g));
    let likeshape = like.querySelector("#text-icon");
  
    if (like.className == 'button-post-comment button-outline-post-comment') {
      sendAjaxRequest('post', '../post/dislike', {id: id})
      console.log("deu dislike");
      like.className = 'button-post-comment';
      likeshape.classList.remove("fa-heart");
      likeshape.classList.add("fa-heart-o");
      likecounter.innerHTML = likecounter.innerHTML.replace(/^[^\d]*(\d+)/g, numberLikes-1);
    } else {
      sendAjaxRequest('post', '../post/like', {id: id})
      console.log("deu like");
      like.className = 'button-post-comment button-outline-post-comment';
      likeshape.classList.remove("fa-heart-o");
      likeshape.classList.add("fa-heart");
      likecounter.innerHTML = likecounter.innerHTML.replace(/^[^\d]*(\d+)/g, numberLikes+1);
    }
  }
  
  function likeComment(id) {
  
    let like = document.querySelector('#countCommentLikes' + id);
    let likecounter = like.querySelector('#text-config');
    let numberLikes = parseInt(likecounter.innerHTML.match(/^[^\d]*(\d+)/g));
    let likeshape = like.querySelector("#text-icon");
  
    if (like.className == 'button-post-comment button-outline-post-comment') {
      sendAjaxRequest('post', '../comment/dislike', {id: id});
      like.className = 'button-post-comment';
      likeshape.classList.remove("fa-heart");
      likeshape.classList.add("fa-heart-o");
      likecounter.innerHTML = likecounter.innerHTML.replace(/^[^\d]*(\d+)/g, numberLikes-1);
  
    } else {
      sendAjaxRequest('post', '../comment/like', {id: id})
      like.className = 'button-post-comment button-outline-post-comment';
      likeshape.classList.remove("fa-heart-o");
      likeshape.classList.add("fa-heart");
      likecounter.innerHTML = likecounter.innerHTML.replace(/^[^\d]*(\d+)/g, numberLikes+1);
    }
  }
  
  async function getNotifications(type) {
    const query = '../api/notifications?type=' + type
    const response = await fetch(query)
    return response.text()
  }
  
  async function getMessages(id) {
    const query = '../api/messages?id=' + id
    const response = await fetch(query)
    return response.text()
  }
  
  async function updateMessages() {
    let id = parseInt(window.location.pathname.toString().match(/\d+/g)[0])
    let section = document.getElementById("msg-page-messages-container")
    let newMessage = await getMessages(id)
    let floor = section.scrollTop + section.clientHeight == section.scrollHeight
    if (newMessage.length > 10) section.innerHTML = section.innerHTML + newMessage
    if (floor) chatScrollBottom()
  }
  
  async function updateNotifications() {
    if (document.querySelector('#notification_content')) {
        document.querySelector('#user_notifications').innerHTML = await getNotifications('user')
        document.querySelector('#post_notifications').innerHTML = await getNotifications('post')
        document.querySelector('#comment_notifications').innerHTML = await getNotifications('comment')
        document.querySelector('#group_notifications').innerHTML = await getNotifications('group')
        updateTotal((document.querySelector('#user_notifications').innerHTML.match(/<article/g) || []).length, 'button_user_notifications');
        updateTotal((document.querySelector('#post_notifications').innerHTML.match(/<article/g) || []).length, 'button_post_notifications');
        updateTotal((document.querySelector('#comment_notifications').innerHTML.match(/<article/g) || []).length, 'button_comment_notifications');
        updateTotal((document.querySelector('#group_notifications').innerHTML.match(/<article/g) || []).length, 'button_group_notifications');
    }
    if (document.querySelector('.notificationspagebutton')) {
        document.querySelector('.notificationspagebutton').innerHTML = await getNotifications('all')
    }
  }
  
  function showCreateGroupForm(){
    document.querySelector("#newgroup").hidden = false;
  }
  
  function cancelCreateGroupForm(){
    document.querySelector("#newgroup").hidden = true;
  }
  
  function updateGroupState(groupID, myID, publicGroup) {
    const state_html = document.querySelector('#groupState' + groupID).innerHTML.replace(/\s/g, '');
    const state = state_html.replace( /(<([^>]+)>)/ig,'');
    switch (state) {
      case 'JoinGroup':
        if (publicGroup) {
  
          sendAjaxRequest('post', 'join', {group_id: groupID});
          document.querySelector('#groupState' + groupID).innerHTML = '<i id="text-icon" class="fa fa-minus-circle" aria-hidden="true"></i> Leave Group';
          updateStatistics(1, 'groupMembers');
  
          let article = document.createElement('article'); 
          article.className = 'member-person-card';
          article.id = 'group-member-' + myID;
          let image = document.createElement('img');
          image.src = getProfileImage(myID);
          image.className = 'user-profile-pic';
          article.appendChild(image);
          let a = document.createElement('a');
          a.href = '../user/' + myID;
          let h2 = document.createElement('h2');
          h2.className = 'user-username group-follow-card-user';
          h2.innerHTML = document.getElementById('user=' + myID).innerHTML;
          a.appendChild(h2);
          article.appendChild(a);
          let h3 = document.createElement('h3');
          h3.className = 'group-follow-card-username';
          h3.innerHTML = document.querySelector('#username' + myID).innerHTML;
          article.appendChild(h3);
          let followers = document.querySelector('#group-members');
          followers.insertBefore(article, followers.firstChild);
          
        } else {
          sendAjaxRequest('post', 'doJoinRequest', {group_id: groupID});
          document.querySelector('#groupState' + groupID).innerHTML = '<i id="text-icon" class="fa fa-times-circle" aria-hidden="true"></i> Cancel join request';
        }
        break;
  
      case 'LeaveGroup':
  
        sendAjaxRequest('post', 'leave', {group_id: groupID});
        document.querySelector('#groupState' + groupID).innerHTML = '<i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Join Group';
        let me = document.querySelector("#group-member-" + myID)
        if (me) me.remove()                
        updateStatistics(-1, 'groupMembers');
        break;
  
      case 'Canceljoinrequest':
  
        sendAjaxRequest('post', 'cancelJoinRequest', {group_id: groupID});
        document.querySelector('#groupState' + groupID).innerHTML = '<i id="text-icon" class="fa fa-plus-circle" aria-hidden="true"></i> Join Group';
        break;
  
      default:
        break;
    }
  }
  
  async function getAPIResult(type, search) {
    const query = '../api/' + type + '?search=' + search
    const response = await fetch(query)
    return response.text()
  }
  
  function acceptJoinRequest(userID, groupID, notificationID) {
    sendAjaxRequest('post', '../group/acceptJoinRequest', {user_id: userID, group_id: groupID})
    let text = document.querySelector('#notification' + notificationID + " > h3")
    text.innerHTML = text.innerHTML.replace("requested", "started")
    document.querySelector('.request').remove()
    document.querySelector('.request').remove()
  
    let button = document.createElement('button');
    button.innerHTML = "X"
    button.className = "notification-button";
    button.onclick = () => removeNotification(notificationID);
    document.querySelector('#notification' + notificationID).appendChild(button);
  }
  
  function rejectJoinRequest(userID, groupID, notificationID) {
    sendAjaxRequest('post', '../group/rejectJoinRequest', {user_id: userID, group_id: groupID})
    document.querySelector('#notification' + notificationID).remove()
  }
  
  function acceptInviteRequest(groupID, notificationID) {
    sendAjaxRequest('post', '../group/acceptInvite', {group_id: groupID})
    document.querySelector('#notification' + notificationID + ' h3.notification-message').innerHTML = document.querySelector('#notification' + notificationID + ' h3.notification-message').innerHTML.replace('invited you to join group', 'you are now member of')
    document.querySelector('.request').remove()
    document.querySelector('.request').remove()
    let button = document.createElement('button');
    button.innerHTML = "X"
    button.className = "notification-button"
    button.onclick = () => removeNotification(notificationID);
    document.querySelector('#notification' + notificationID).appendChild(button);
  }
  
  function rejectInviteRequest(groupID, notificationID) {
    sendAjaxRequest('post', '../group/rejectInvite', {group_id: groupID})
    document.querySelector('#notification' + notificationID).remove()
  }
  
  function showComments(id, type) {
    let comments = document.querySelector('#' + type + id + ' footer')
    comments.hidden ? comments.hidden = false : comments.hidden = true;
  }
  
  function deleteComment(id, previousID) {
    sendAjaxRequest('post', '../comment/delete', {id: id})
    document.querySelector('#comment'+id).remove()
    if(document.querySelector('.subcomment'+id)) document.querySelector('.subcomment'+id).remove()
    if(previousID === "commentResults") updateStatistics(-1, previousID); 
    else updateStatisticsHTML(-1, previousID);
  }
  
  function reply(id) {
    let target_comment = document.querySelector('#owner' + id).parentElement;
    let target_username = document.querySelector('#owner' + id).innerHTML;
    let temp_comment = target_comment.parentElement.parentElement.lastElementChild;
    console.log(temp_comment);
    if (!temp_comment.querySelector('#content-textarea').value.match('@' + target_username))
      if (temp_comment.querySelector('#content-textarea').value === '') 
        temp_comment.querySelector('#content-textarea').value = '@' + target_username;
      else
        temp_comment.querySelector('#content-textarea').value = temp_comment.querySelector('#content-textarea').value + ' @' + target_username;
  }
  
  function updateTotal(quantity, id) {
    let statistic = document.getElementById(id)
    if (statistic) {
      statistic.innerHTML = statistic.innerHTML.replace(/\d+/g, quantity)
    }
  }
  
  function removeGroupMember(groupID, memberID){
    sendAjaxRequest('post', '/group/removeMember', {group_id: groupID, member_id: memberID});
    document.querySelector('#group-member-'+memberID).remove();
    updateStatistics(-1, 'groupMembers');
  }
  
  function inviteToGroup(){
    document.querySelector(".inviteToGroup").style.visibility = "visible";
    document.querySelector('#close-button').onclick = function() { document.querySelector(".inviteToGroup").style.visibility = "hidden"; };
  }
  
  async function sendEditComment(id) {
  
    let comment = document.querySelector('#comment' + id);
    let content_place = comment.querySelector("#main-content");
    let target = comment.querySelector('textarea');
    let newcontent = await parseContentSend(target.value);
    newcontent = newcontent.replace("\n", "<br />");
  
    if (target.value == '') {
      alert("Empty comment. Please try again.");
      return;
    }
  
    if (newcontent.length > 255) {
      alert("Large comment. Please try again.");
      return;
    }
  
    sendAjaxRequest('post', '../comment/edit', {id: id, content: target.value});
  
    let h3 = document.createElement('h3');
    h3.className = 'content'
    h3.innerHTML = newcontent;
  
    content_place.insertBefore(h3, target);
    content_place.removeChild(target);
  
    //change button edit to confirm
    let edit_button = document.querySelector("#editComment" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-floppy-o");
    edit_button_icon.classList.add("fa-pencil");
  
    let button = document.querySelector('#editComment' + id);
    button.onclick = () => {
        editComment(id);
    }
  }
  
  function editComment(id) {
  
    let comment = document.querySelector('#comment' + id);
    let content_place = comment.querySelector("#main-content");
    let target = content_place.querySelector('h3.content');
  
    let textarea = document.createElement('textarea');
    textarea.type = 'textbox';
    textarea.value = parseContentEdit(target.innerHTML);
  
    content_place.insertBefore(textarea, target);
    content_place.removeChild(target);
  
    //change button edit to confirm
    let edit_button = document.querySelector("#editComment" + id);
    let edit_button_icon = edit_button.querySelector("#text-icon");
    edit_button_icon.classList.remove("fa-pencil");
    edit_button_icon.classList.add("fa-floppy-o");
  
    let button = document.querySelector('#editComment' + id);
    button.onclick = () => {
        sendEditComment(id);
    }
  }
  
  function inviteState(userID, groupID) {
  
    let button = document.querySelector('#inviteGroup' + groupID);
    if (button.innerHTML == 'Cancel Invitation') {
      sendAjaxRequest('post', '../group/cancelInvite', {user_id: userID, group_id: groupID});
      button.innerHTML = 'Invite';
    } else {
      sendAjaxRequest('post', '../group/invite', {user_id: userID, group_id: groupID});
      button.innerHTML = 'Cancel Invitation';
    }
  }
  
  function favorite(groupID) {
  
    let button = document.querySelector('#fav' + groupID);
    if (button.className == 'group-interaction-button fa fa-star') {
      sendAjaxRequest('post', '../group/unfavorite', {id: groupID})
      button.className = 'group-interaction-button fa fa-star-o'
    } else {
      sendAjaxRequest('post', '../group/favorite', {id: groupID})
      button.className = 'group-interaction-button fa fa-star'
    }
  }
  
  async function search(input) {
    document.querySelector('#results-posts').innerHTML = await getAPIResult('post', input);
    document.querySelector('#results-users').innerHTML = await getAPIResult('user', input)
    document.querySelector('#results-groups').innerHTML = await getAPIResult('group', input)
    document.querySelector('#results-comments').innerHTML = await getAPIResult('comment', input)
    updateTotal((document.querySelector('#results-posts').innerHTML.match(/class="main-post"/g) || []).length, 'postResults');
    updateTotal((document.querySelector('#results-users').innerHTML.match(/<article/g) || []).length, 'userResults');
    updateTotal((document.querySelector('#results-groups').innerHTML.match(/<article/g) || []).length, 'groupResults');
    updateTotal((document.querySelector('#results-comments').innerHTML.match(/class="comment"/g) || []).length, 'commentResults');
  }
  
  function deleteGroup(groupID){
    sendAjaxRequest('post', '../group/delete', {id: groupID});
    window.location.href = '/groups';
  }
  
  async function parseContentSend(content){
  
    async function replaceAsync(str, regex, asyncFn) {
      const promises = [];
      str.replace(regex, (match, ...args) => {
          const promise = asyncFn(match, ...args);
          promises.push(promise);
      });
      const data = await Promise.all(promises);
      return str.replace(regex, () => data.shift());
    }
  
    async function taggingVerification(user) {
      const query = '../api/userVerify?search=' + user.replace("@","");
      const response = await fetch(query).then( r => r.json());
      if(response != -1) {return "<a href='../user/"+response+"'>"+user+"</a>";}
      else {return user;}
    }
  
    content = content.replace(/(#\w+)/g, "<a href='../home/search?query=$1'>$1</a>");
  
    const contentfix = await replaceAsync(content, /(@\w+)/g , taggingVerification);
  
    return contentfix;
  }
  
  function parseContentEdit(content){
    return content.replace(/(<([^>]+)>)/ig, "");
  }
  
  async function getContext(id, type) {
    const query = '../api/context?type=' + type + "&id=" + id;
    const response = await fetch(query)
    return response.text()
  }
  
  async function showContext(id, type) {
    document.querySelector('#notification_popup').innerHTML = await getContext(id, type) + '<button class="notification-button" onclick="cancelPopup()">X</button>';
    document.querySelector('#notification_popup').hidden = false ;
    document.querySelector('#configuration_popup').hidden = true;
  }
  
  function showConfigurations() {
    document.querySelector('#configuration_popup').hidden = false;
    document.querySelector('#notification_popup').hidden = true;
  }
  
  function cancelPopup() {
    document.querySelector('#notification_popup').hidden = true;
    document.querySelector('#configuration_popup').hidden = true;
  }
  
  async function contacts(input) {
    document.querySelector('#results-users').innerHTML = await getAPIResult('user', input)
    updateTotal((document.querySelector('#results-users').innerHTML.match(/<article/g) || []).length, 'userResults');
  }
  
  function toggleSidebar() {
    let sidebar = document.querySelector('#sidebar');
    let openSideBarBtn = document.querySelector("#sidebar-toggle-open");
    if( sidebar.className === 'sidebar-hidden'){
      sidebar.className = 'sidebar-shown';
      openSideBarBtn.hidden = false;
      openSideBarBtn.classList.add('open-sidebar-button-hidden');
      openSideBarBtn.classList.remove('open-sidebar-button-shown');
    }
    else{
      sidebar.className = 'sidebar-hidden';
      openSideBarBtn.hidden = false;
      openSideBarBtn.classList.add('open-sidebar-button-shown');
      openSideBarBtn.classList.remove('open-sidebar-button-hidden');
    }
  }
  
  function chatScrollBottom() {
    const chatBox = document.getElementById("msg-page-messages-container");
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  
  function searchChats(){
    let input = document.getElementById('sidebar-chat-search').value;
    input = input.toLowerCase();
    let list = document.getElementById('sidebar-current-chat-list');
    let listElems = list.getElementsByTagName('article');
    console.log(listElems);
  
    for (i = 0; i < listElems.length; i++) {
      let name = listElems[i].querySelector(".chat-person-name")
      let username = listElems[i].querySelector(".chat-person-username")
      if (!name.innerHTML.toLowerCase().includes(input) && !username.innerHTML.toLowerCase().includes(input)) {
        listElems[i].style.display="none";
      }
      else {
        listElems[i].style.display="flex";
      }
    }
  }
  
  function submitOnEnter(event){
    if(event.which === 13 && !event.shiftKey){
        event.target.form.dispatchEvent(new Event("submit", {cancelable: true}));
        event.preventDefault(); // Prevents the addition of a new line in the text field
    }
  }
  
  function confirmation(action, args){
    document.getElementById('confirm').addEventListener('click', function() {action(...args)});
    document.getElementById('modalEnable').click();
  }
  
  function init() {
  
    updateNotifications()
    setInterval(updateNotifications, 2000)
  
    if (window.location.toString().match(/message\//g) != null) {
      setInterval(updateMessages, 2000)
      chatScrollBottom()
    }
  
    const search_bar = document.querySelector("#search")
    if (search_bar) {
      let initial_input = window.location.toString().match(/query=(.*)$/g);
      if (initial_input != null) {
        search_bar.value = initial_input[0].replace('query=', '');
        search(initial_input[0].replace('query=', '').replace('#', ''));
      }
      search_bar.addEventListener('input', async function() {
        let input = this.value.replace('#', '');
        search(input);
      })
    }
  
    const contacts_bar = document.querySelector("#search-contacts")
      if (contacts_bar) {
        contacts_bar.addEventListener('input', async function() {
          contacts(this.value);
        })
    }
  
    const message_form = document.querySelector("#new-message-form")
    if (message_form) {
      const message_textarea = message_form.querySelector("#content-textarea")
      if(message_textarea){
      message_textarea.addEventListener("keypress", submitOnEnter);
      }
    }
  }
  
  init()  