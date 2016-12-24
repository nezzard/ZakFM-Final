var app = require('http').createServer()
var io = require('socket.io')(app);
var request = require('request');
var WPAPI = require( 'wpapi' );
var promise = require( 'promise' );
var fs = require( 'fs' );
var reload = require( 'require-reload' );
var rp = require('request-promise');/*
var async = require('async');
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




io.on('connection', function (socket) {


  socket.emit('getList');

  socket.on('playlist', function (data) {
    var result = JSON.parse(data);
    
  function writet(myJson){
		fs.writeFile( "filename.json", JSON.stringify( myJson ), "utf8" );
		console.log('11111111111111111111111111');
			//	console.log(myJson);

  }
var myJson = new Array();

for(var i = 0; i <= result.length-1;  ) {

  function test(i) {
  setTimeout(function(){
  	//console.log(result[i]);

  		wp.song().search( result[i]['artist']).then(function( posts ) {
  				console.log(wp.media().id( posts[0]['featured_media'] ));
					return posts[0]['featured_media'];
  		}).then(function(data){  			
		  	wp.song().search( result[i]['artist']+' - '+result[i]['song'] ).then(function( posts ) {
		  				


							if(!posts[0]){
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
												console.log(response.id);
												myJson.push({key: result[i]['id'], post: response});
												writet(myJson);


										});

								    
								  })
								  .catch(/* handle error*/);


							}else {
								
								console.log("Посты найдены");
								myJson.push({key: result[i]['id'], post: posts, end: data});
								writet(myJson);

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

									  fs.writeFile("/tmp/img/"+artist+".png", body, 'binary', function(err) {
									    if(err){
									      console.log('err');
									    }
									    else{

									      	console.log("The file was saved!");

														wp.media()
												    .file( '/tmp/img/'+artist+'.png' )
												    .create({
												        title: responsed.artist[0],
												        alt_text: 'an image of something awesome',
												        caption: 'This is the caption text',
												        description: 'More explanatory information'
												    })
												    .then(function( responses ) {
															 return wp.song().id( responsed.id ).update({
																  featured_media: responses.id
																});
												    })

									    }


									  }); 

		
								});


						}
						else {
							console.log('not find')
						}
							myJson.push({key: result[i]['id'], post: responsed});
												writet(myJson);
							return  Promise.resolve(responsed);

					})

			  })
			  .catch(/* handle error */);			});
	
	
		


  }, 2000 * i);

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
