import re

with open('/Applications/XAMPP/xamppfiles/htdocs/Cloud-Arena/Game Server Rental Platform/database.sql', 'r') as f:
    lines = f.readlines()

# find the START of the new schema
start_idx = 0
for i, line in enumerate(lines):
    if "CREATE DATABASE IF NOT EXISTS cloud_arena;" in line:
        start_idx = i
        break

new_lines = lines[start_idx:]

content = "".join(new_lines)

# Fix user IDs in users insert
content = content.replace(
    "INSERT INTO users (username, password, email, full_name, role, status, credit) VALUES",
    "INSERT INTO users (id, username, password, email, full_name, role, status, credit) VALUES"
)
content = content.replace(
    "('admin', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'admin@cloudarena.local', 'Administrator', 'admin', 'active', 1000),",
    "(1, 'admin', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'admin@cloudarena.local', 'Administrator', 'admin', 'active', 1000),"
)
content = content.replace(
    "('testuser', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'test@cloudarena.local', 'Test User', 'member', 'active', 200),",
    "(2, 'testuser', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'test@cloudarena.local', 'Test User', 'member', 'active', 200),"
)
content = content.replace(
    "('testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', 'member', 'active', 0),",
    "(4, 'testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', 'member', 'active', 0),"
)
content = content.replace(
    "('guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', 'member', 'active', 0);",
    "(8, 'guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', 'member', 'active', 0);"
)

# Fix contacts missing inserts
contacts_missing = """(8, 3, 'Khách', 'khach@gmail.com', 'Ticket mới từ Khách', 'Test chức năng noti - khach@gmail.com', 'unread', '2026-05-10 03:35:10'),
(8, 5, 'baaaaaa', 'ba@gmail.com', 'Ticket mới từ baaaaaa', 'weneedba - ba@gmail.com', 'unread', '2026-05-10 03:51:06'),
(8, 6, 'giang', 'giang@gmail.com', 'Ticket mới từ giang', 'need test - giang@gmail.com', 'unread', '2026-05-10 03:51:38'),
"""
# insert before the semi-colon of contacts insert
content = content.replace(
    "(8, 4, 'Instructor Test', 'admin@example.com', 'Asking', 'aaaaaaaaaaaaaa', 'replied', '2026-05-10 09:21:09');",
    "(8, 4, 'Instructor Test', 'admin@example.com', 'Asking', 'aaaaaaaaaaaaaa', 'replied', '2026-05-10 09:21:09'),\n" + contacts_missing[:-2] + ";"
)

with open('/Applications/XAMPP/xamppfiles/htdocs/Cloud-Arena/Game Server Rental Platform/new_database.sql', 'w') as f:
    f.write(content)
