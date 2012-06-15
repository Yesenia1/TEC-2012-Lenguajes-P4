require ('rubygems')
gem ('twitter')
require ('twitter')

class Tweets
  def initialize(client)
    @@client=client
  end

  def tweetear
    #Utiliza el cliente creado en la autenticacion para enviar tweets a su cuenta
    puts "\nDigite el mensaje que desea enviar:"
    mensaje = gets.chomp
    @@client.update(mensaje)
    puts "Su status ha sido actualizado"
  end

  def mensaje
    puts "\n Digite el mensaje que desea enviar:"
    mensaje = gets.chomp
    seguidores = @@client.follower_ids()
    seguidores.each { |k,v|  if "#{k}"=="ids"
                                v.each { |f|@@client.direct_message_create( f , mensaje)}
                            end
                    }
  end

  
end
