require 'compass/import-once/activate'
require 'autoprefixer-rails'

# Require any additional compass plugins here.
sourcemap = (environment == :production) ? false : true

on_stylesheet_saved do |file|
  css = File.read(file)
  map = file + '.map'

  if File.exists? map
    result = AutoprefixerRails.process(
      css,
      from: file,
      to:   file,
      map:  { prev: File.read(map), inline: false })
    File.open(file, 'w') { |io| io << result.css }
    File.open(map,  'w') { |io| io << result.map }
  else
    File.open(file, 'w') { |io| io << AutoprefixerRails.process(css) }
  end
end

add_import_path "public/bower_components"

# Set this to the root of your project when deployed:
http_path = "public"
sass_dir = "resources/assets/sass"
css_dir = "resources/assets/stylesheets"
fonts_path = "public/fonts"
http_fonts_path = "/fonts"
javascripts_dir = "public/javascripts"
images_dir = "public/media_assets/images"

# You can select your preferred output style here (can be overridden via the command line):
# output_style = :expanded or :nested or :compact or :compressed
output_style = :expanded

# To enable relative paths to assets via compass helper functions. Uncomment:
relative_assets = false

# To disable debugging comments that display the original location of your selectors. Uncomment:
line_comments = false

preferred_syntax = :sass
