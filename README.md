iTunes Library Parsing Class
============================

PHP Class to parse an iTunes Library XML file into an array of objects.

Example usage
-------------

    <?php
    $libraryPlaylistPath = "path_to_library.xml";
    $library = Itunes_Library::import_library_xml($libraryPlaylistPath);

    echo '<pre>';
    print_r($library->info);
    print_r($library->tracks);
    print_r($library->playlists);
    echo '</pre>';
    ?>