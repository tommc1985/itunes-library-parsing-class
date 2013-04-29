<?php
/**
 * Itunes_Library - Class the allows user to parse an exported iTunes XML Playlist/Library
 * and convert it into an object
 *
 * @class Itunes_Playlist
 * @author Thomas McGregor <leegleeders@yahoo.co.uk>
 * @link http://www.thomasmcgregor.co.uk
 * @copyright Copyright (c) 2012, Thomas McGregor
 * @version 0.9.0
 */

class Itunes_Library {
    /**
     * string   Path to the XML file
     * @static
     */
    public static $file_path;
    /**
     * SimpleXML Object   The Parse XML as a SimpleXML object
     * @static
     */
    public static $xml;

    /**
     * Import Songs from XML iTunes export
     * @param string            Path to iTunes Library XML File
     * @param int               Offset position
     * @param int|false         Return either a maximum number of rows or set to false to return all
     * @return mixed            Library Info
     */
    public static function import_library_xml($file_path, $offset = 0, $limit = false)
    {
        try {
            // Check file exists
            if (file_exists($file_path)) {
                self::$file_path = $file_path;
                // Check SimpleXML is installed
                if (function_exists('simplexml_load_file')) {
                    // Instantiate new playlist object
                    $library = new stdClass();

                    // Load XML from file
                    self::$xml = simplexml_load_file(self::$file_path);

                    // Parse Application Info
                    $library->info = self::parse_export_info();

                    // Parse Track Info
                    $library->tracks = self::parse_tracks($offset, $limit);

                    // Parse Playlist Info
                    $library->playlists = self::parse_playlists();

                    return $library;
                } else {
                    // SimpleXML not installed
                    throw new Exception('SimpleXML does not appear to be installed on this server');
                }
            } else {
                // XML File does not exist
                throw new Exception("File does not exist");
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            return false;
        }
    }

    /**
     * Parse Playlist Info from xml, return object
     * @return object           Returned Object of Library Info
     */
    public static function parse_export_info()
    {
        $info = (object) array('major_version' => (int) self::$xml->dict->integer[0],
            'minor_version' => (int) self::$xml->dict->integer[1],
            'date' => (string) self::$xml->dict->date,
            'application_version' => (string) self::$xml->dict->string[0],
            'features' => (int) self::$xml->dict->integer[2],
            'music_folder' => (string) self::$xml->dict->string[1],
            'library_persistent_id' => (string) self::$xml->dict->string[2]);

        return $info;
    }

    /**
     * Parse Track Info from xml, return array
     * @param int               Offset position
     * @param int|false         Return either a maximum number of rows or set to false to return all
     * @return array            Array of songs
     */
    public static function parse_tracks($offset, $limit)
    {
        $tracks = array();

        // Initalise Track Counter
        $i = 0;

        // If Offset is not an integer
        if (!is_int($offset)) {
            $offset = $i;
        }

        // If limit is an integer
        if (is_int($limit)) {
            $limit = $offset + $limit;
        }

        // Loop through track nodes
        foreach(self::$xml->dict->dict->dict as $song) {
            if ($i >= $offset && ($limit === false || $i < $limit)) {
                // List properties related to track
                $keys = array();
                foreach($song->key as $key) {
                    $keys[] = (string) $key;
                }

                $track = new stdClass();

                $string_index = 0;
                $integer_index = 0;
                $date_index = 0;

                $track->track_id = in_array("Track ID", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->name = in_array("Name", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->artist = in_array("Artist", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->album_artist = in_array("Album Artist", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->composer = in_array("Composer", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->album = in_array("Album", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->genre = in_array("Genre", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->kind = in_array("Kind", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->size = in_array("Size", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->total_time = in_array("Total Time", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->disc_number = in_array("Disc Number", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->disc_count = in_array("Disc Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->track_number = in_array("Track Number", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->track_count = in_array("Track Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->year = in_array("Year", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->date_modified = in_array("Date Modified", $keys) ? (string) $song->date[$date_index++] : NULL;
                $track->date_added = in_array("Date Added", $keys) ? (string) $song->date[$date_index++] : NULL;
                $track->bit_rate = in_array("Bit Rate", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->sample_rate = in_array("Sample Rate", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->comments = in_array("Comments", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->play_count = in_array("Play Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->play_date = in_array("Play Date", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->play_date_utc = in_array("Play Date UTC", $keys) ? (string) $song->date[$date_index++] : NULL;
                $track->rating = in_array("Rating", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->album_rating = in_array("Album Rating", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->release_date = in_array("Release Date", $keys) ? (string) $song->date[$date_index++] : NULL;
                $track->normalization = in_array("Normalization", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->artwork_count = in_array("Artwork Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->series = in_array("Series", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->season = in_array("Season", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->episode = in_array("Episode", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->episode_order = in_array("Episode Order", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->sort_album = in_array("Sort Album", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->persistent_id = in_array("Persistent ID", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->content_rating = in_array("Content Rating", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->track_type = in_array("Track Type", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->protected = in_array("Protected", $keys) ? true : false;
                $track->purchased = in_array("Purchased", $keys) ? true : false;
                $track->podcast = in_array("Podcast", $keys) ? true : false;
                $track->unplayed = in_array("Unplayed", $keys) ? true : false;
                $track->has_video = in_array("Has Video", $keys) ? true : false;
                /*HD - No easy way to identify if HD flag is true or false*/
                $track->video_width = in_array("Video Width", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->video_height = in_array("Video Height", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->movie = in_array("Movie", $keys) ? true : false;
                $track->tv_show = in_array("TV Show", $keys) ? true : false;
                $track->music_video = in_array("Music Video", $keys) ? true : false;
                $track->location = in_array("Location", $keys) ? (string) $song->string[$string_index++] : NULL;
                $track->file_folder_count = in_array("File Folder Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;
                $track->library_folder_count = in_array("Library Folder Count", $keys) ? (int) $song->integer[$integer_index++] : NULL;

                $tracks[] = $track;

            }
            $i++;
        }

        return $tracks;
    }

    /**
     * Parse Playlist Info from xml, return array
     * @return array            Array of playlists
     */
    public static function parse_playlists()
    {
        $playlists = array();

        // Loop through playlist nodes
        foreach(self::$xml->dict->array->dict as $playlist) {
            // List properties related to playlist
            $keys = array();
            foreach($playlist->key as $key) {
                $keys[] = (string) $key;
            }

            $object = new stdClass();

            $string_index = 0;
            $integer_index = 0;

            $object->name = in_array("Name", $keys) ? (string) $playlist->string[$string_index++] : NULL;
            $object->playlist_id = in_array("Playlist ID", $keys) ? (int) $playlist->integer[$integer_index++] : NULL;
            $object->playlist_persistent_id = in_array("Playlist Persistent ID", $keys) ? (string) $playlist->string[$string_index++] : NULL;

            $object->tracks = array();

            // If playlist has at least one track in it
            if (count($playlist->array->dict) > 0) {
                // Loop through track ids
                foreach ($playlist->array->dict as $track_id) {
                    $object->tracks[] = (int) $track_id->integer;
                }
            }

            $playlists[] = $object;
        }

        return $playlists;
    }
}
