Loading a file into the STEEM blockchain
======

The question arose:

> Is it possible to get files into the STEEM blockchain?

With a few little tricks you can get everything into the STEEM blockchain.

What is needed:
------

- A STEEM account
- One file
- Some know-how in programming
    - Our example scripts are written in PHP

We tried to make the tutorial as simple as possible. 
However, some prerequisites are necessary, so we classify this for experienced users.


What am I learning:
------
    
- How to split files with PHP
- How can I upload files to the STEEM blockchain
- How can I reassemble split files with PHP


*The following scripts are for testing purposes and not for productive use*

General procedure
------

- We split our file into small pieces
    - This is because STEEM has a maximum length of a post
- We convert each section into text
    - This is because we get binary data only badly in a post
- We write a post / comment for each section 
    - The content of a post is the content of a file
- We read every single post and put all content back together again
    

Tutorial
------

- All examples are written in PHP, but the procedure can be implemented in any language imaginable. 
- Let us take Steemit's white paper as an example.