CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL CHECK(role IN ('requester', 'staff', 'admin')),
    department TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    building TEXT NOT NULL,
    floor TEXT NOT NULL,
    category TEXT NOT NULL,
    capacity INTEGER NOT NULL DEFAULT 1,
    manager TEXT NOT NULL,
    contact TEXT NOT NULL,
    status TEXT NOT NULL CHECK(status IN ('available', 'limited', 'maintenance')),
    open_hours TEXT NOT NULL,
    summary TEXT NOT NULL,
    equipment TEXT NOT NULL DEFAULT '',
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    room_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    requester_name TEXT NOT NULL,
    department TEXT NOT NULL,
    start_at TEXT NOT NULL,
    end_at TEXT NOT NULL,
    attendees INTEGER NOT NULL DEFAULT 1,
    purpose TEXT NOT NULL,
    requirements TEXT,
    status TEXT NOT NULL CHECK(status IN ('pending', 'approved', 'rejected')) DEFAULT 'pending',
    staff_status TEXT NOT NULL CHECK(staff_status IN ('scheduled', 'ready', 'in_use', 'cleanup', 'issue')) DEFAULT 'scheduled',
    rejection_reason TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_bookings_room_time ON bookings(room_id, start_at, end_at);
CREATE INDEX idx_bookings_user ON bookings(user_id, start_at);
