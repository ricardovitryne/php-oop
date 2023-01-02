create table users
(
    iduser           int auto_increment
        primary key,
    email            varchar(100)  not null,
    name             varchar(100)  not null,
    password         varchar(100)  null,
    drinkcounter     int default 0 null,
    token            varchar(150)  null,
    token_expiration datetime      null
);

create table user_drinks
(
    id             int auto_increment,
    user_iduser    int           not null,
    date_reference date          not null,
    count          int default 0 not null,
    primary key (id, user_iduser),
    constraint unique_key
        unique (date_reference, user_iduser),
    constraint user_drinks_users_null_fk
        foreign key (user_iduser) references users (iduser)
);