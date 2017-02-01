var app = require('http').createServer()
var io = require('socket.io')(app);
var request = require('request');
var WPAPI = require( 'wpapi' );
var promise = require( 'promise' );
var fs = require( 'fs' );
var reload = require( 'require-reload' );
var convert = require('cyrillic-to-latin')
var chalk = require('chalk');
var axios = require('axios');

var _ = require('lodash');
var chunk = require('lodash.chunk');


var rp = require('request-promise');
var async = require('async');/*
var syncrequest = require('sync-request');*/

require( "console-stamp" )( console, { pattern : "dd/mm/yyyy HH:MM:ss.l" } );

app.listen(7080);
var wp = new WPAPI({
    endpoint: 'http://zakarpattyafm.com.ua/wp-json',
    username: 'socket',
    password: ')C(%sW$2CGzqFD#*GcGXFnyh'
});




wp.song = wp.registerRoute('wp/v2', '/song/(?P<id>\\d+)', {
   methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']
});

wp.arch = wp.registerRoute('wp/v2', '/arch', {
   methods: [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']
});




// Конец функции

var people = {};
var count;
var history = [ ];


io.on('connection', function (socket) {

var myJsonn = new Array();
function writet(myJson){
	console.log(myJson.count);
	
	console.log(count + 'Test');

	myJsonn.push(myJson);

	if(count === myJson.count){
			socket.broadcast.emit('sendSongg', JSON.stringify(myJsonn));

	}

	fs.writeFile( "filename.json", JSON.stringify( myJsonn ), "utf8" );

	count++;


	
}	







// Функция добавления поста

function postSong(songArray, myJson, count){
	console.log(songArray + " Добавление " );
	console.log(count + 'абракадабра');

 // myJson = '';

	songArray = JSON.parse(songArray);
	var artist = songArray[0]['artist'];
	var song = songArray[0]['song'];


console.log(artist);

/*
	var myRequests = [];
	myRequests.push(rp({uri: "http://ws.audioscrobbler.com/2.0/?method=artist.search&artist="+encodeURIComponent(artist)+"&api_key=603b0439073b39ec6b890756f4345933&format=json", json: true}));
	myRequests.push(rp({uri: "https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(artist+'+'+song)+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", json: true}));



*/





  var myRequests = [
    axios.get("http://ws.audioscrobbler.com/2.0/?method=artist.search&artist="+encodeURIComponent(artist)+"&api_key=603b0439073b39ec6b890756f4345933&format=json"),
    axios.get("https://www.googleapis.com/youtube/v3/search?part=snippet&order=relevance&q="+encodeURIComponent(artist+'-'+song)+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4")
  ];
return 	Promise.all(myRequests)


		.then((arrayOfHtml) => arrayOfHtml.map(result => result.data))

	  	.then((arrayOfHtml) => {

  		
	  		console.log(JSON.stringify(arrayOfHtml[0].results.artistmatches.artist.length));
	  		//Пытаемся получить картинку от ласт фм, если нету, выдаем пустую строку
		  	if(arrayOfHtml[0].results.artistmatches.artist.length  > 0){
		  		last = arrayOfHtml[0].results.artistmatches.artist[0]['image'][2]['#text'];
		  	}else {
		  		last = '';
		  	}


		  	//Пытаемся получить видео от ютуб, если нету, выдаем пустую строку
		  	if(arrayOfHtml[1]['items'][0]) {
		  		yout = arrayOfHtml[1]['items'][0]['id'].videoId;
		  	}
		  	else {
		  		yout = false;
		  	}

		  	//Создаем пост с песней
			return wp.song().create({
			    title: artist+' - '+song,
			    artist: artist,
			    song: song,
			    youtube: yout,
			    status: 'publish'
			}).then(function( responsed ) {

			//После создания поста, проверяем есть ли картинка, если да, загружаем ее и прикрепляем как миниатюру
				if(last){
					request.get({url: last, encoding: 'binary'}, function (err, response, body) {
						//Чистим Артиста от лищних символов
						artist = responsed.artist[0].replace(/\s+/g, '');
						artist = artist.replace(/(\.)|([1-9])/g, "");

						fs.writeFile("/tmp/img/"+convert(artist)+".png", body, 'binary', function(err) {
							if(err){
							    console.log('Ошибка при загрузке изображения на сервер');
							}
							else{
							    console.log("Изображение сохранено!");

							    //Изображение сохранилось, загружаем его в Wordpress
								wp.media().file( '/tmp/img/'+convert(artist)+'.png' )
								.create({
									title: responsed.artist[0]
								})
								.then(function( responses ) {
									//Обновляем пост, прикрепляем миниатюру
									wp.song().id( responsed.id ).update({
									  featured_media: responses.id
									})
									//Возвращаем айпи миниатюры 
									console.log(chalk.red(myJson + ' 1 JSON'));
									return responses.id;
								})
								.then(function(mediaId){
									//Проверяем успешность и прикрепление изображения к песне
									wp.media().id( mediaId ).then(function(media){
								  		//return myJson.push({key: 1, post: responsed, end: media.guid.rendered});
								  		writet({key: songArray[0]['id'], post: responsed, end: media.guid.rendered, count: count});
								  		console.log(chalk.red(mediaId + ' тест'));
								  		//Запускаеми сохранение в JSON

								  		console.log(chalk.red(myJson + ' 2 JSON'));
								  		// writet(myJson);
									}).then(function(){
										//Удаляем изображение из папки tmp
								  		fs.unlinkSync("/tmp/img/"+convert(artist)+".png", function(err){
								  			console.log('Ошибка в удалении файла'+ err);
								  		});
								  	})
								  	.catch(function(mediaErr){
								  		console.log("Ошибка media"+mediaErr);
								  	})				    	
								}).catch(function(mediaErr){
								  		console.log("Ошибка media 2"+mediaErr);
								})	
							};

						}); 

					});
				//Конец IF, загрузка изрбражения
				}
				else {
					//Если изображения нету, отправляем массив без изрбражения
					console.log('not find');
					//myJson.push({key: 1, post: responsed, end: 0});
					writet({key: songArray[0]['id'], post: responsed, end: 0, count: count});
				//	writet(myJson);

				};								
				return  Promise.resolve(responsed);
			});
		//Конец promise (than)	
		})
	  	.catch(function(errorCreate2){
	  		//Ловим ошибку promise
	  		console.log(chalk.red("Ошибка promise  " + JSON.stringify(errorCreate2)));
		});

	  return myJson;
console.log(chalk.blue(myJson + 'fcsdfsdf'));

	  //socket.broadcast.emit('sendSongg', myJson);

}

	socket.on('getJsonChat', function(){
    if (history.length > 0) {
    		io.emit('ChatJson', JSON.stringify(history));
    }		
	})


	
socket.emit('count', count);





  socket.on('join', function(name){

    people[socket.id] = name;
    
  });

  socket.on('send', function(msg, who, role){
  	console.log(who);
    io.emit('chat', who, msg, role);
		var obj = {
      who: who,
      msg: msg,
      role: role
    };   
    history.push(obj);
    history = history.slice(-300);
  });


	socket.on('songchanged', function(changed){
		var songdata = JSON.parse(changed);







		console.log("Тепер грає " + chalk.green(songdata[0]['artist'] + " | " + songdata[0]['song']));


		if(/undefined/.test(String(songdata[0]['artist'])) === false &&
		   /undefined/.test(String(songdata[0]['song'])) === false &&
		   /UNKNOWN/.test(String(songdata[0]['artist'])) === false &&
		   /UNKNOWN/.test(String(songdata[0]['song'])) === false &&
		   /Джингл/.test(String(songdata[0]['artist'])) === false &&
		   /Default/.test(String(songdata[0]['artist'])) === false &&
		   /Default/.test(String(songdata[0]['song'])) === false &&
		   /Джингл/.test(String(songdata[0]['song'])) === false &&
		   /Пошта/.test(String(songdata[0]['song'])) === false &&
		   /Пошта/.test(String(songdata[0]['artist'])) === false) {

			rp("http://ws.audioscrobbler.com/2.0/?method=artist.search&artist="+encodeURIComponent(songdata[0]['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json")
	    	.then(function (htmlString) {
	    	var image = JSON.parse(htmlString).results.artistmatches.artist[0]['image'][2]['#text'];
	      	socket.broadcast.emit('changed', {"image": image, "changed": songdata[0]});
	      
	      	console.log(songdata[0]['changed']);
		    if(songdata[0]['changed'] === true){      	
				wp.song().search( songdata[0]['artist']+' - '+songdata[0]['song'] ).then(function( posts ) {
				   wp.arch().create({
				   	songid: posts[0].id
				   }).then(function(er){
				   	console.log(er);	   
				   	console.log(chalk.blue('Archived' + songdata[0]['artist']+' - '+songdata[0]['song']));   	
				   }).catch(function(e){

				   	
				   })
				}).catch(function(e){
					//console.log('Не найдено ');

					//ПЕРЕВІРИТИ ЧИ ДОДАЄ, КОЛИ ПІСНЮ ДОДАЛИ В ТЕПЕР ГРАЄ
					socket.emit("playlist", {id: 0, song: songdata[0]['song'], artist: songdata[0]['artist']})
				})      	
			} else {
				//console.log(chalk.magenta('Изменений в текущей песни не обнаружено'));
			}
		     // console.log(chalk.blue('Отправил изменения текущей песни'));
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

count = 1;

var myJson = new Array();






result.reduce((lastRequestDone, item) => {
	

	var songArray = new Array ({'key': item['id'], 'artist': item['artist'], 'song': item['song']});
	songArray = JSON.stringify(songArray);


  				return lastRequestDone.then(() => 
  				wp.song().search( item['artist']).then(function( posts ) {
  				//Берем миниатюру из первого поста, если артист найден
					return posts[0]['featured_media'];
  				}).then(function(data){  			
		  			return lastRequestDone.then(() => wp.song().search( item['artist']+' - '+item['song'] ).then(function( posts ) {
		  				
		  				//Ищем композицию по артисту и названию песни
		  						if(!posts[0]){
		  							 return lastRequestDone.then(() => postSong(songArray, myJson, result.length));
		  						}
		  						else{
									//Если посты найдены, пушим их в массив и отправляем клиенту
									console.log("Посты найдены ");

									if(posts[0]['featured_media'] === 0){
										//Отправляем клиенту массив БЕЗ миниатюрой 
										//myJson.push({key: result[i]['id'], post: posts[0], end: 0});
										return lastRequestDone.then(() => writet({key: item['id'], post: posts[0], end: 0, count: result.length}));
										 console.log(chalk.yellow('Зашлдо'));
										
									}
					  				wp.media().id( posts[0]['featured_media'] ).then(function(media){
					  					//Отправляем клиенту массив с миниатюрой 
					  					//myJson.push({key: result[i]['id'], post: posts[0], end: media.guid.rendered});

					  					return lastRequestDone.then(() => writet({key: item['id'], post: posts[0], end: media.guid.rendered, count: result.length}));
					  					

					  				});			  							
		  						}
										

				  		}));
  				}).catch(function( err ) {

					    return lastRequestDone.then(() => postSong(songArray, myJson, result.length));


// Тут должна быть функция, если нету картинки в исполнителе который уже был postSong()			

				}));

  	//		writet(myJson);



  }, Promise.resolve());


  });
	
socket.on('seyGet', function(){
	myJson = reload("./filename.json");
	socket.emit('sendSongg' , JSON.stringify(myJson));

})

});

