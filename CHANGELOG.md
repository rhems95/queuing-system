# Changelog

Notable behavior of the PECIT queue system. Older capstone notes that say SMS fires at five ahead are out of date.

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
