#include Enumerable
require_relative 'tweet'


class Inicio
    def initialize(client)
    @@client= Tweets.new(client)
    end
    
    def tweet()
    @@client.tweetear
    end
  
end

