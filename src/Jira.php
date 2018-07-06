<?php

namespace Univerze\Jira;

class Jira
{

    /**
     * Search function to search issues with JQL string
     *
     * @param null $jql
     * @return mixed
     */
    public static function search( $jql = NULL )
    {
        $data   = json_encode( array( 'jql' => $jql ) );
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
        $data   = json_encode( array( 'fields' => $data ) );
        $data = str_replace('\\\\','\\',$data);
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
        $data = json_encode( array( 'fields' => $data ) );
        $data = str_replace('\\\\','\\',$data);
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
    protected static function request( $request, $data, $is_post = 0, $is_put = 0 )
    {
        $ch = curl_init();

        curl_setopt_array( $ch, array(
            CURLOPT_URL            => config( 'jira.url' ) . '/rest/api/2/' . $request,
            CURLOPT_USERPWD        => config( 'jira.username' ) . ':' . config( 'jira.password' ),
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => array( 'Content-type: application/json' ),
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
