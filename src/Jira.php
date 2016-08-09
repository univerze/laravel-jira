<?php

namespace Univerze\Jira;

class Jira
{

    /**
     * Handling conncetions on demand
     * 
     * @param type $connection
     * @return \self
     */
    public static function connection( $connection = NULL )
    {
        if( is_string( $connection ) )
        {
            config( ['jira.connection' => $connection] );
        } elseif( is_array( $connection ) )
        {
            config( ['jira.connection' => 'custom'] );
            config( ['jira.connections.custom.url' => $connection[0]] );
            config( ['jira.connections.custom.username' => $connection[1]] );
            config( ['jira.connections.custom.password' => $connection[2]] );
        }
        return new self;
    }

    /**
     * Search function to search issues with JQL string
     *
     * @param null $jql
     * @return mixed
     */
    public static function search( $jql = NULL )
    {
        $data   = json_encode( array('jql' => $jql) );
        $result = self::request( 'search', $data );

        return json_decode( $result );
    }

    /**
     * Create function to create a single issue from array data
     *
     * @param array $data
     * @return mixed
     */
    public static function create( array $data )
    {
        $data   = json_encode( array('fields' => $data) );
        $data   = str_replace( '\\\\', '\\', $data );
        $result = self::request( 'issue', $data, 1 );

        return json_decode( $result );
    }

    /**
     * Update function to change existing issue attributes
     *
     * @param string $issue
     * @param array $data
     * @return mixed
     */
    public static function update( $issue, array $data )
    {
        $data   = json_encode( array('fields' => $data) );
        $data   = str_replace( '\\\\', '\\', $data );
        $result = self::request( 'issue/' . $issue, $data, 0, 1 );

        return json_decode( $result );
    }

    /**
     * CURL request to the JIRA REST api (v2)
     *
     * @param $request
     * @param $data
     * @param int $is_post
     * @param int $is_put
     * @return mixed
     */
    private static function request( $request, $data, $is_post = 0, $is_put = 0 )
    {
        $ch = curl_init();

        $connection = config( 'jira.connection', config( 'jira.default' ) );
        curl_setopt_array( $ch, array(
            CURLOPT_URL            => config( "jira.connections.{$connection}.url" ) . '/rest/api/2/' . $request,
            CURLOPT_USERPWD        => config( "jira.connections.{$connection}.username" ) . ':' . config( "jira.connections.{$connection}.password" ),
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURLOPT_RETURNTRANSFER => 1,
        ) );

        if( $is_post )
        {
            curl_setopt( $ch, CURLOPT_POST, 1 );
        }

        if( $is_put )
        {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
        }

        $response = curl_exec( $ch );

        curl_close( $ch );

        return $response;
    }

}