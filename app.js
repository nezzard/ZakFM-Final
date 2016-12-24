var app = require('http').createServer()
var io = require('socket.io')(app);
var request = require('request');
var WPAPI = require( 'wpapi' );
var promise = require( 'promise' );
var fs = require( 'fs' );
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

			


for(var i = 0; i <= result.length-1; i++) {

  (function(i) {
  setTimeout(function(){

  		wp.song().search( result[i]['artist']).then(function( posts ) {
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
										    featured_media: data,
										    status: 'publish'
										}).then(function( response ) {
												console.log(response.id);

												//wp.nextp().id(response.id).ordernum(result[i]['id']).create({})

												socket.broadcast.emit('sendSong', response);

										});

								    
								  })
								  .catch(/* handle error*/);


							}else {
								//console.log("Посты найдены")
								wp.nextp().id(response.id).ordernum(result[i]['id']).create({})
							}

		  		})

  		}).catch(function( err ) {
			    console.log(1);


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
					    youtube: yout,
					    status: 'publish'
					}).then(function( responsed ) {
						socket.emit('test', 'test');

						if(arrayOfHtml[0]['artist']['image'][2]['#text']){

								request.get({url: last, encoding: 'binary'}, function (err, response, body) {
									artist = responsed.artist[0].replace(/\s+/g, '');

									  fs.writeFile("/tmp/img/"+artist+".png", body, 'binary', function(err) {
									    if(err){
									      console.log(err);
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


							return  Promise.resolve(responsed);


					})

			    
			  })
			  .catch(/* handle error */);









			});


  }, 2000 * i);
  })(i);
}




		
/*
result.forEach(function(item){
	wp.song().search( item['artist']+' - '+item['song'] ).then(function( posts ) {
		if(!posts[0]){

			var myRequests = [];
			myRequests.push(rp({uri: "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+encodeURIComponent(item['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json", json: true}));
			myRequests.push(rp({uri: "https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(item['artist']+'+'+item['song'])+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", json: true}));
			Promise.all(myRequests)
			  .then((arrayOfHtml) => {
			  	//console.log(arrayOfHtml[0]['artist']['image'][2]['#text']);
			    //console.log(arrayOfHtml[1]['items'][0]['id'].videoId);


					wp.song().create({
					    title: item['artist']+' - '+item['song'],
					    content: 'Your post content',
					    youtube: arrayOfHtml[1]['items'][0]['id'].videoId,
					    lastfm: arrayOfHtml[0]['artist']['image'][2]['#text'],
					    status: 'publish'
					}).then(function( response ) {
					    console.log( response.id );
					})

			    
			  })
			  .catch(/* handle error );


		}else {
			console.log("Посты найдены")
		}
	});
})
*/


/*

		async.eachSeries(result, function(item, callback) {



/*

			var res = syncrequest("GET", "https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(item['artist']+'+'+item['song'])+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", {
			  'headers': {
			    'user-agent': 'example-user-agent'
			  }
			});

			if(JSON.parse(res.getBody('utf8'))['items'][0] !== undefined) {
				temp.push({name: count, number: JSON.parse(res.getBody('utf8'))['items'][0].id.videoId});	
			} 
			else {
				temp.push({name: count, number: false});	
			}





			var res2 = syncrequest("GET", "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+encodeURIComponent(item['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json", {
			  'headers': {
			    'user-agent': 'example-user-agent'
			  }
			});

			if(JSON.parse(res2.getBody('utf8'))['artist'] !== undefined) {
				temp2.push({name: count, number: JSON.parse(res2.getBody('utf8'))['artist']['image'][2]['#text']});	
			} 
			else {
				temp2.push({name: count, number: false});	
			}


			callback();
   
		}, 
		function(err) {
		  if( err ) {
		    console.log('Ошибка случилась');
		  } else {
		    console.log('Все хорошо едем дальше');
		    console.log(temp);
		    console.log(temp2);
		  }
		});


*/







/*

		async.eachSeries(result, function(item, callback) {
		  request.get("https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+encodeURIComponent(item['artist']+'+'+item['song'])+"&type=video&key=AIzaSyAx41IMvqqZYxuUQ-MQ1oMJmZBikIrnfw4", function(err, res, body)
		  {
		    if(res.statusCode == 200 ) {     
			    if(JSON.parse(body)['items'][0] !== undefined) {
			    	temp.push({name: count, number: JSON.parse(body)['items'][0].id.videoId});		       
			  	} 
			  	else {
			    	temp.push({name: count, number: false});
			  	}
		 			callback();
		 			count++;
		  	}
	 			else {
		      callback('Error');
		    }
		        
		  });    
		}, 
		function(err) {
		  if( err ) {
		    console.log('Ошибка случилась');
		  } else {
		    console.log('Все хорошо едем дальше');
		    console.log(temp);
		  }
		});




var that = this;

		async.eachSeries(result, function(item, callback2) {
		  request.get("http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist="+encodeURIComponent(item['artist'])+"&api_key=603b0439073b39ec6b890756f4345933&format=json", function(err, res, body)
		  {
		    if(res.statusCode == 200 ) {     
			    if(JSON.parse(body)['artist'] !== undefined) {
			    	temp2.push({name: count2, number: JSON.parse(body)['artist']['image'][2]['#text']});		       
			  	} 
			  	else {
			    	temp2.push({name: count2, number: false});
			  	}
		 			callback2();
		 			count2++;
		  	}
	 			else {
		      callback2('Error');
		    }
		        
		  });    




		}, that);


console.log(temp2);

*/

/*

	result.forEach(function(items) {

			request.get("https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+items['song']+'+'+items['song']+"&type=video&key=AIzaSyAvDAdEnqrStOJNnpnGy9BkrC_sG-gcHIU", function(err,res,body){
          if(res.statusCode == 200 ) {          	
	           console.log(JSON.parse(body)['items'][0].id.videoId);	         
          }
        });

	});

		var k;
    var timeout = 0;

		for (k in result) {


request.get("https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q="+result[k]['song']+'+'+result[k]['song']+"&type=video&key=AIzaSyAvDAdEnqrStOJNnpnGy9BkrC_sG-gcHIU", function(err,res,body){
          if(res.statusCode == 200 ) {
          	
	            var c = JSON.parse(body);
	         		doOtherAction(c['items'][0]['id']['videoId']);
	         
          }
        });

timeout += 5000;


		}

	function doOtherAction (item) {
	console.log(1);
  console.log(item);

}*/

  });
});

