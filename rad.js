var fs = require('fs'),
  request = require('request');

request
  .get('http://195.234.148.51:8000/;stream.mp3')
  .on('error', function(err) {
    // handle error
  })
  .pipe(fs.createWriteStream('2.mp3'));

