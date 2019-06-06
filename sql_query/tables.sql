CREATE TABLE USER(
    User_id         VARCHAR(16)     NOT NULL,
    User_password   VARCHAR(32)     NOT NULL,
    User_name       VARCHAR(16),

    PRIMARY KEY (User_id)
);

CREATE TABLE SERIES(
    Series_id       INTEGER         NOT NULL AUTO_INCREMENT,
    Title           VARCHAR(128)    NOT NULL,
    Synopsis        VARCHAR(1024),
    Author          VARCHAR(16),
    Cover_path      VARCHAR(128),

    PRIMARY KEY (Series_id)
);

CREATE TABLE EPISODE(
    Series_id       INTEGER         NOT NULL,
    Episode_id      INTEGER         NOT NULL,
    Title           VARCHAR(128),
    Cover_path      VARCHAR(128),
    Update_time     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (Series_id, Episode_id),
    FOREIGN KEY (Series_id) REFERENCES SERIES(Series_id)
);

CREATE TABLE IMAGELIST(
    Series_id       INTEGER         NOT NULL,
    Episode_id      INTEGER         NOT NULL,
    Image_number    INTEGER         NOT NULL,
    Image_path      VARCHAR(128)    NOT NULL,

    PRIMARY KEY (Series_id, Episode_id, Image_number),
    FOREIGN KEY (Series_id, Episode_id) REFERENCES EPISODE(SERIES_ID, EPISODE_ID)
);

CREATE TABLE SUBSCRIBE(
    User_id         VARCHAR(16)     NOT NULL,
    Series_id       INTEGER         NOT NULL,

    PRIMARY KEY (User_id, Series_id),
    FOREIGN KEY (User_id) REFERENCES USER(User_id),
    FOREIGN KEY (Series_id) REFERENCES SERIES(Series_id)
);

CREATE TABLE EVALUATION(
    User_id         VARCHAR(16)     NOT NULL,
    Series_id       INTEGER         NOT NULL,
    Episode_id      INTEGER         NOT NULL,
    Value           INTEGER         NOT NULL,

    PRIMARY KEY (User_id, Series_id, Episode_id),
    FOREIGN KEY (User_id) REFERENCES USER(User_id),
    FOREIGN KEY (Series_id, Episode_id) REFERENCES EPISODE(Series_id, Episode_id)
);

CREATE TABLE BOOKMARK(
    User_id         VARCHAR(16)     NOT NULL,
    Series_id       INTEGER         NOT NULL,
    Episode_id      INTEGER         NOT NULL,

    PRIMARY KEY (User_id, Series_id, Episode_id),
    FOREIGN KEY (User_id) REFERENCES USER(User_id),
    FOREIGN KEY (Series_id, Episode_id) REFERENCES EPISODE(Series_id, Episode_id)
);

CREATE TABLE COMMENT(
    User_id         VARCHAR(16)     NOT NULL,
    Series_id       INTEGER         NOT NULL,
    Episode_id      INTEGER         NOT NULL,
    Content         VARCHAR(4096)   NOT NULL,
    Update_time     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (User_id, Series_id, Episode_id),
    FOREIGN KEY (User_id) REFERENCES USER(User_id),
    FOREIGN KEY (Series_id, Episode_id) REFERENCES EPISODE(Series_id, Episode_id)
);

CREATE TABLE NOTIFICATION(
    Notification_id INTEGER         NOT NULL,
    User_id         VARCHAR(16)     NOT NULL,
    Series_id       INTEGER,
    Episode_id      INTEGER,
    Message         VARCHAR(256)    NOT NULL,
    Update_time     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Notified        TIMESTAMP,

    PRIMARY KEY (Notification_id, User_id),
    FOREIGN KEY (User_id) REFERENCES USER(User_id),
    FOREIGN KEY (Series_id, Episode_id) REFERENCES EPISODE(Series_id, Episode_id)
);