ghe_host = "github.com"

# If you use GHE, you can pass in -h <hostname>
system("gh config set -h #{ghe_host} git_protocol https")

# We have to use a proxy socket at work, you might not
#system("gh config set http_unix_socket #{ENV['HOME']}/.ourproxy")

puts "You need a GitHub access token now"
puts "Press enter to confirm and open the token page in your browser, where you'll create an API token"
puts "Come back here and paste in the token when you're done."
puts
print "press enter to continue > "
$stdin.gets

# Modify scopes/description to taste
token_url = "https://#{ghe_host}/settings/tokens/903293593/regenerate"

#system("open #{token_url}")

puts
print "paste your API token and press enter > "

api_token = $stdin.gets.strip

#Tempfile.create('/tmp/api-token') do |file|
  #file.chmod(0600) # keep other users from reading in the file
  #file.write(api_token)
  #kfile.close

#end
  system("gh auth login -h #{ghe_host} --with-token api_token")

puts
puts "Set your API token"

print "Testing if gh is authenticated... "

if system("gh auth status -h #{ghe_host}")
  puts "OK!"
else
  puts "ERROR"
  puts
  puts "gh is not authenticated"
  exit 1
end
