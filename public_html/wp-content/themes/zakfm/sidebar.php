            	<!-- Сайдбар -->
                <aside class="aside">
                	<div class="one-widget">
                    	<h3 class="wrap-tit">
                        	Наступні композиції
                        </h3>
                        <div class="will-play">
                        	<div class="all-songs">

                                

                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="one-widget">
                    	<div class="social">
                            <a href="https://vk.com/zakarpattyafm">
                              <img src="<?php bloginfo('template_url'); ?>/images/vk.png" alt=" " />
                            </a>
                        	  <a href="https://www.facebook.com/zakarpattyafm/">
                            	<img src="<?php bloginfo('template_url'); ?>/images/facebook.png" alt=" " />
                            </a>
                            
                            <a href="http://tunein.com/radio/Zakarpattya-FM-1019-s112621/">
                              <img src="<?php bloginfo('template_url'); ?>/images/tune.png" alt=" " />
                            </a>
                        </div>
                        <div class="viber">
                            <div class="viber-tit">

                            </div>
                            <div class="viber-thumb">
                              <img src="<?php bloginfo('template_url'); ?>/images/viber.png">
                            </div>
                            <div class="viber-number">
                              <div class="viber-number-in">
                               <span>Пишіть нам в Viber!</span>
                               +380-50-432-04-33
                              </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="one-widget rekl">
                    	<a href="#">
                        	<img src="<?php echo get_field('перед_чатом', 'option'); ?>">
                        </a>
                    </div>
                    
                    <div class="one-widget chat">
                      <div class="chat-tit">
                        Чат
                      </div>
                      <div class="body-chat">
                          <div class="avat-user">
                              <div class="avat-thumb">
                              <img src="<?php bloginfo('template_url'); ?>/images/rad.png" alt=" " />
                              </div>
                              <div class="avat-name">
                                  <div class="avat-name-in">
                                  Закарпаття ФМ
                                  <!--span>
                                  Онлайн чат (104 учасника)
                                  </span-->
                                </div>
                              </div>
                          </div>

                          <div class="row">
                            <div class="span2">
                              <ul id="people" class="unstyled"></ul>
                            </div>
                            <div class="span4">
                              <ul id="msgs" class="unstyled"></ul>
                            </div>
                          </div>

                          <div class="send-form">
                            <div class="row">
                              <div class="span5 offset2" id="login">
                                <form class="form-inline noLogged" >
                                  <input type="text" class="input-small chat-inp" placeholder="Ваше ім'я" id="name">
                                  <input type="button" name="join" id="join" value="Увійти" class="btn-ok">
                                </form>
                              </div>

                              <div class="span5 offset2" id="chat">
                                <form id="2" class="form-inline-msg">
                                  <input type="text" class="input chat-inp" placeholder="Ваше повідомлення" id="msg">
                                  <input type="button" name="send" id="send" value="Відправити" class="btn-ok">
                                </form>
                              </div>
                            </div>
                          </div>
                      </div>
   
                    </div>


                    <div class="one-widget rekl">
                      <a href="#">
                          <img src="<?php echo get_field('після_чата', 'option'); ?>">
                        </a>
                    </div>

                    
                </aside>
                <!-- Конец Сайдбар -->