create table attendees
(
    id int auto_increment,
    uuid varchar(36) not null,
    surname varchar(255) not null,
    given_name varchar(255) not null,
    email varchar(255) null,
    phonenumber varchar(50) null,
    street varchar(255) null,
    house_nr varchar(10) null,
    zip_code varchar(10) null,
    city varchar(255) null,
    state varchar(50) null,
    country char(3) default 'DEU' null,
    chip int(5) null,
    privacy_policy tinyint(1) default 0 not null,
    constraint attendees_id_uindex
        unique (id),
    constraint attendees_uuid_uindex
        unique (uuid)
);

alter table attendees
    add primary key (id);

create table check_events
(
    eid int auto_increment
        primary key,
    aid int not null,
    time datetime not null,
    event enum('checkin', 'checkout') not null,
    chip int(5) null,
    constraint check_events_attendees_id_fk
        foreign key (aid) references attendees (id)
            on delete cascade
);

create table detail_verification
(
    id int auto_increment
        primary key,
    user int not null,
    credential enum('CORE_DATA', 'EMAIL', 'PHONE_NUMBER') null,
    challenge varchar(255) null,
    challenge_date datetime default current_timestamp() null,
    verification_date datetime null,
    constraint detail_verification_attendees_id_fk
        foreign key (user) references attendees (id)
            on delete cascade
);

create table users
(
    id int auto_increment,
    username varchar(256) not null,
    password varchar(512) not null,
    given_name varchar(256) null,
    surname varchar(255) null,
    constraint users_id_uindex
        unique (id),
    constraint users_username_uindex
        unique (username)
);

alter table users
    add primary key (id);

create table audit_log
(
    id int auto_increment
        primary key,
    time datetime default current_timestamp() not null,
    user int null,
    action varchar(256) not null,
    data varchar(4096) not null,
    constraint audit_log_users_id_fk
        foreign key (user) references users (id)
);

create table verification_data
(
    id int auto_increment
        primary key,
    aid int not null,
    vaccination_status tinyint(1) null,
    vaccination_date date null,
    recovery_status tinyint(1) null,
    recovery_date date null,
    test_status tinyint(1) null,
    test_datetime datetime null,
    test_type varchar(256) null,
    test_agency varchar(256) null,
    privacy_policy tinyint(1) not null,
    constraint verification_date_attendees_id_fk
        foreign key (aid) references attendees (id)
            on delete cascade
);

