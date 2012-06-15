
#Gems y clases utilizadas
require ('rubygems')
gem ('twitter')
require ('twitter')
require ('oauth')
require ('launchy')

require_relative 'inicio'
require_relative 'tweet'

    # se define los consumer-token y Consumer-secret de la aplicacion
    c_token = "nRb4k6GeloYGqgbp8mngA"
    c_secret = "XH3HR8UOMowYO35MIdjG39fjRBJG1smE9eVZIb4gkI"

    # se define un cliente que mediante el token y secret accese a la pagina de twitter, autorizando la app
    cliente = OAuth::Consumer.new(c_token,c_secret, :site=>"http://twitter.com",
                                                    :request_token_url => "https://api.twitter.com/oauth/request_token",
                                                    :access_token_url => "https://api.twitter.com/oauth/access_token",
                                                    :authorize_url => "https://api.twitter.com/oauth/authorize")
    request_token = cliente.get_request_token
    r_token = request_token.token
    r_secret = request_token.secret
    #el api da respuesta para autenticar el usuario, se agrega un r_token para que detecte la solicitud anterior
    puts " yendo a Twitter a autorizar su cuenta"
    Launchy.open(cliente.authorize_url + "?oauth_token=" + r_token)
       
    #Una vez autenticado en twitter y el app, retorna un codigo que debe ingresar para poder continuar la ejecucion de la app
    puts "Digite el codigo:"
    pin = gets.chomp
    
    # se verifica el cliente ( los tokens y secrets) y se configura el twitter, se crea el cliente basado en la configuracion y verifica sus credenciales

    begin
      OAuth::RequestToken.new(cliente, r_token, r_secret)
      access_token=request_token.get_access_token(:oauth_verifier => pin)
      Twitter.configure do |config|
                        
      config.consumer_key = c_token
      config.consumer_secret = c_secret
      config.oauth_token = access_token.token
      config.oauth_token_secret = access_token.secret
     end

    client = Twitter::Client.new
    client.verify_credentials
    rescue Twitter::Unauthorized
    puts "Fallo"
    end

  puts "\n\nBienvenido a GrooveTweet"
  aplicacion = Inicio.new(client)
  aplicacion.tweet(tweetear.to_i)

 
  

