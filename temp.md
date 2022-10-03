User ()
Follower (idUser1 -> User, idUser2 -> User, accept) // se accept = True, então é um follower, senão é um pedido
Post (id, idOwner -> User)
Comment (id, idPost -> Post, idOwner -> User)
Grupo (id, idOwner -> User, accept)
Member (idUser -> User, idGroup -> Group, accept) // se accept = True, então é membro, senão é um pedido
Media (id, idPost -> Post, type enum generalization)

// Problema: notificações

