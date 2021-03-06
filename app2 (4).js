var app = require('http').createServer()
var io = require('socket.io')(app);
var request = require('request');
var WPAPI = require( 'wpapi' );
var promise = require( 'promise' );
var fs = require( 'fs' );
var reload = require( 'require-reload' );
var convert = require('cyrillic-to-latin')
var chalk = require('chalk');

var rp = require('request-promise');
var async = require('async');/*
var syncrequest = require('sync-request');*/


app.listen(7080);
var wp = new WPAPI({
    endpoint: 'http://zakarpattyafm.com.ua/wp-json',
    username: 'admin',
    password: 'sS0952607540'
});




wp.song = wp.registerRoute('wp/v2', '/song/(?P<id>\\d+)', {
   methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']
});

wp.arch = wp.registerRoute('wp/v2', '/arch', {
   methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']
});

var people = {};




io.on('connection', function (socket) {
 
  socket.on('join', function(name){
    people[socket.id] = name;
    console.log(people[socket.id]);

  });

  socket.on('send', function(msg){
    io.emit('chat', people[socket.id], msg);
  });


	socket.on('songchanged', function(changed){
		var songdata = JSON.parse(changed);







		if(/UNKNOWN/.test(String(songdata[0]['artist'])) === false || /UNKNOWN/.test(String(songdata[0]['song'])) === false || /джинг/.test(String(songdata[0]['artist'])) === false || /Default/.test(String(songdata[0]['artist'])) === false || /Default/.test(String(songdata[0]['song'])) === false || /Джингл/.test(String(songdata[0]['song'])) === false) {

			rp("http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+encodeURIComponent(songdata[0]['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json")
	    	.then(function (htmlString) {
	    	var image = JSON.parse(htmlString).artist['image'][2]['#text'];
	      	socket.broadcast.emit('changed', {"image": image, "changed": songdata[0]});
	      
	      
		    if(songdata[0]['changed'] === true){      	
				wp.song().search( songdata[0]['artist']+' - '+songdata[0]['song'] ).then(function( posts ) {
				   wp.arch().create({
				   	songid: posts[0].id
				   }).then(function(er){
				   	console.log(er);	      	
				   }).catch(function(e){

				   	
				   })
				}).catch(function(e){
					console.log('Не найдено ');

					//ПЕРЕВІРИТИ ЧИ ДОДАЄ, КОЛИ ПІСНЮ ДОДАЛИ В ТЕПЕР ГРАЄ
					socket.emit("playlist", {id: 0, song: songdata[0]['song'], artist: songdata[0]['artist']})
				})      	
			} else {
				console.log(chalk.magenta('Изменений в текущей песни не обнаружено'));
			}
		      console.log(chalk.blue('Отправил изменения текущей песни'));
		    })
		    .catch(function (err) {
		        // Crawling failed...
		    });

		} 
		//Если нашли джингл, анкновн и т.д.
		else {
			socket.broadcast.emit('changed', {"image": false, "changed": {"artist": "Закарпаття ФМ", "song": "Радіо"}});
		}
		

	});
	

		



  socket.emit('getList');

  socket.on('playlist', function (data) {
    var result = JSON.parse(data);
    console.log(result);


  function writet(myJson){
		fs.writeFile( "filename.json", JSON.stringify( myJson ), "utf8" );
		
  }
var myJson = new Array();




var ir = 0;
for(var i = 0; i <= result.length-1;  ) {

		//songUnk = result[i]['song'];
		//artUnk = result[i]['artist'];

		console.log(chalk.red(result[i]['song']));
		templ = /\bUNKNOWN\b/;

  function test(i) {
  setTimeout(function(){
  	console.log(result[i]);


  	


  	if(ir === 5){
  		socket.broadcast.emit('sendSongg', myJson);
  	}
		if(ir < 5){
			var songUnk = result[i]['song'];
		var artUnk = result[i]['artist'];





			if( /UNKNOWN/.test(String(result[i]['artist'])) === false && /UNKNOWN/.test(String(result[i]['song'])) === false){
	console.log(chalk.green(/UNKNOWN/.test(String(result[i]['artist']))));
console.log(chalk.green(/UNKNOWN/.test(String(result[i]['song']))));			

  		wp.song().search( result[i]['artist']).then(function( posts ) {
  				//Берем миниатюру из первого поста, если артист найден
					return posts[0]['featured_media'];
  		}).then(function(data){  			
		  	wp.song().search( result[i]['artist']+' - '+result[i]['song'] ).then(function( posts ) {
		  				//Ищем композицию по артисту и названию песни
							if(!posts[0]){
								//Если не песня не найдена, добавляем ее в базу, с использованием миниатюры выше
								var myRequests = [];
								myRequests.push(rp({uri: "https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(result[i]['artist']+'+'+result[i]['song'])+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", json: true}));
								Promise.all(myRequests)
								  .then((arrayOfHtml) => {
							  	if(arrayOfHtml[0]['items']) {
							  		yout = arrayOfHtml[0]['items'][0]['id'].videoId;
							  	}
							  	else {
							  		yout = "";
							  	}

										wp.song().create({
										    title: result[i]['artist']+' - '+result[i]['song'],
										    content: 'Your post content',
										    youtube: yout,
										    artist: result[i]['artist'],
										    song: result[i]['song'],
										    featured_media: data,
										    status: 'publish'
										}).then(function( response ) {

									      wp.arch().create({
									      	songid: response.id
									      }).then(function(er){
									      	console.log(er);	      	
									      })

												console.log(response.id);
												myJson.push({key: result[i]['id'], post: response});
												writet(myJson);


										}).then(function(errorCreate){
											console.log(chalk.red('Ошибка добавления 1' + errorCreate));
										});

								    
								  })
								  .catch(/* handle error*/);


							}else {
								//Если посты найдены, пушим их в массив и отправляем клиенту
								console.log("Посты найдены ");

								if(posts[0]['featured_media'] === 0){
									//Отправляем клиенту массив БЕЗ миниатюрой 
									myJson.push({key: result[i]['id'], post: posts[0], end: 0});
									writet(myJson);
								}
			  				wp.media().id( posts[0]['featured_media'] ).then(function(media){
			  						//Отправляем клиенту массив с миниатюрой 
			  						myJson.push({key: result[i]['id'], post: posts[0], end: media.guid.rendered});
			  						writet(myJson);

			  				})

								

							}

		  		})
  		}).catch(function( err ) {



			var myRequests = [];
			myRequests.push(rp({uri: "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+encodeURIComponent(result[i]['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json", json: true}));
			myRequests.push(rp({uri: "https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(result[i]['artist']+'+'+result[i]['song'])+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", json: true}));
			Promise.all(myRequests)
			  .then((arrayOfHtml) => {
			  	if(arrayOfHtml[0]['artist']['image'][2]['#text']){
			  		last = arrayOfHtml[0]['artist']['image'][2]['#text'];
			  		console.log(last);
			  	}else {
			  		last = '';
			  	}

			  	if(arrayOfHtml[1]['items'][0]) {
			  		yout = arrayOfHtml[1]['items'][0]['id'].videoId;
			  	}
			  	else {
			  		yout = "";
			  	}


					wp.song().create({
					    title: result[i]['artist']+' - '+result[i]['song'],
					    content: 'Your post content',
					    artist: result[i]['artist'],
					    song: result[i]['song'],
					    youtube: yout,
					    status: 'publish'
					}).then(function( responsed ) {

						if(arrayOfHtml[0]['artist']['image'][2]['#text']){

								request.get({url: last, encoding: 'binary'}, function (err, response, body) {
									artist = responsed.artist[0].replace(/\s+/g, '');
									artist = artist.replace(/(\.)|([1-9])/g, "");


									  fs.writeFile("/tmp/img/"+convert(artist)+".png", body, 'binary', function(err) {

									    if(err){
									      	console.log('err');
									    }
									    else{
									      	console.log("The file was saved!");

											wp.media()
											.file( '/tmp/img/'+convert(artist)+'.png' )
											.create({
											    title: responsed.artist[0],
											    alt_text: 'an image of something awesome',
											    caption: 'This is the caption text',
											    description: 'More explanatory information'
											})
											.then(function( responses ) {
												wp.song().id( responsed.id ).update({
												  featured_media: responses.id
												})
												return responses.id;
											}).then(function(mediaId){
													wp.media().id( mediaId ).then(function(media){
										  			myJson.push({key: result[i]['id'], post: responsed, end: media.guid.rendered});
										  			return writet(myJson);

										  		}).then(function(ttt){
										  			fs.unlinkSync("/tmp/img/"+convert(artist)+".png", function(err){
										  				console.log('Ошибка в удалении файла'+ err);
										  			});
										  		})				    	
											})
									    }


									  }); 

		
								});


						}
						else {
							console.log('not find');
							myJson.push({key: result[i]['id'], post: responsed, end: 0});
							writet(myJson);

						}

												
							return  Promise.resolve(responsed);

					})

			  })
			  .catch(function(errorCreate2){
			  	console.log(chalk.red("Ошибка добавления 2 " +errorCreate2));
			  });			

			});


  		ir++;
			console.log(chalk.green('Unknown Не найден'));


			}
			else {
				console.log(chalk.red('Найден Unknown'));
			}


		}

	



  }, 4000 * i);
		console.log(chalk.yellow(i));
  }
  test(i);
 	

 		i++;


}	


  });
	
socket.on('seyGet', function(){
	myJson = reload("./filename.json");
	socket.emit('sendSongg' , myJson);

})

});

