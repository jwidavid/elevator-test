The Elevator Test (In PHP!)
==========================================

<b>DESCRIPTION</b>

First there is an elevator class.
It has a direction (up, down, stand, maintenance), a current floor and a list of floor requests sorted in the direction.
Each elevator has a set of signals: Alarm, Door open, Door close

The scheduling will be like:
if available pick a standing elevator for this floor.
else pick an elevator moving to this floor.
else pick a standing elevator on another floor.

Sample data:
- Elevator standing in first floor
- Request from 6th floor go down to ground(first floor).
- Request from 5th floor go up to 7th floor
- Request from 3rd floor go down to ground
- Request from ground go up to 7th floor.
- Floor 2 and 4 are in maintenance.

Extra Point: Making an API to send/receive requests to elevator and write log file.


<b>ASSUMPTIONS</b>

In order to fulfill this challenge without further clarification, several assumptions must be made.

1) Assumption: Only one elevator is required

Explanation: The second paragraph suggests that more than one elevator exist. However, the "Sample data" suggests that only one elevator will be required. If this challenge only requires one elevator then the second paragraph is rendered meaningless. 


2) Assumption: I can determine for format of elevator commands.

Explanation: The challenge description does not specify in what format the elevator commands are provided.


3) Assumption: Set of signals is used for logging purposes only.

Explanation: The description requires the signals but does not clarify in what way they should be used. My assumption is that this challenge was part of a larger challenge that was downsized but not refactored for clarity.


4) Assumption: The application is intended to be stateless.

Explanation: Typically with an elevator, one command comes in at a time; however, the challenge does not mention any form of statefulness (such as use of a database). This means that all the commands must be submitted at once. I have developed the application to separate a set of commands into two - the second command entering the queue after the first has been served. In this way my intention is to simulate a real-world environment as much as is possible.


<b>LIST TO-DO</b>

1) <strike>Correct the problems with the current flowchart</strike>

2) <strike>Add the 'floors in maintenance' feature</strike>

3) Properly document code

4) Create API service

5) <strike>Add the logging feature</strike>
    - Add function to output logging to a log file

6) Optimize code:
    - Parsing of commands 
    - Insertions of new requests (without needing to do re-sort each time)
    - ?
