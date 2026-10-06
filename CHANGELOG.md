# Changelog

Notable behavior of the PECIT queue system. Older capstone notes that say SMS fires at five ahead are out of date.

## 2026-10-06

### Printer paper SMS

- Admin → **Kiosk settings** stores staff or assistant mobiles. Each row has a **Send SMS** checkbox.
- A checked number gets one text when the kiosk printer first runs out of paper: `PECIT: Kiosk printer is out of paper. Load a new roll.`
- An unchecked number stays on the list and is not texted.
- Loading a new roll clears the sent mark, so the next time the paper runs out the text can go out again.
- The text uses the same modem outbox as student notices. Printing and Call Next do not wait on it.

### Walk-in screen token

- Each walk-in ticket gets a token such as `T1006ABC`: weekday initial, month, day, and three random letters.
- The cooldown screen shows the token. The printed ticket and the public display do not.
- Staff see the token while that walk-in is being served, so they can match a photo of the screen.
- If the walk-in entered a mobile number, the ticket text includes the token.

### SMS sender

- The modem's saved message center was blank, so text-mode sends failed. The sender now includes the Smart message center in each text.
- Starting the sender again no longer stops a sender that is already running. A stuck text prompt is cancelled before the next try.

## 2026-10-02

### SMS (SIM7600)

- Opt-in only. Students check **Notify me by SMS** on confirm. The screen shows the last four digits of the stored mobile.
- Three texts, numbers only: the ticket number after print, a come-back text when the ticket first has 10 or fewer ahead in fair order, and the window name when staff calls or recalls.
- A ticket already within 10 at print does not get the come-back text. There is no separate “you are next” text.
- The queue does not wait on the modem. PHP writes `storage/app/sms-outbox`; `tools/kiosk/Watch-GsmSms.ps1` sends the text.
- No COM port is set in the app. The sender uses the SIM7600 port whose name includes **AT**.
- The modem, its driver, and the sender stay on the kiosk/server PC. Staff PCs do not need the driver.

### Walk-in mobile

- After the kiosk PIN, the issue screen can take an optional mobile on its own number pad. The Windows keyboard does not open.
- Blank means no text. Any other value must be a Philippine mobile: `09` and 9 more digits.
- **Issue & Print** stays disabled until the number is blank or valid. The server rejects other numbers.

### Kiosk hardware

- Confirm accepts a Student ID keypad or an ACR122U tap. `GET /kiosk/card` starts the reader watcher if it is not running.
- Staff window and System Float show a paper-out alert when the XP-Q90EC roll is out or a ticket stays stuck in Printing. Issued tickets stay valid.
- The printed 58mm ticket is shorter: smaller type and tighter line spacing. The on-screen number is unchanged.
- The walk-in issue screen fits a **1024×600** kiosk without scrolling.

### Records

- `students.card_uid` and `students.mobile` store the card serial and SMS number.
- `queues.sms_mobile`, `sms_issued`, and `sms_near` remember the opt-in number and which notices were queued.
