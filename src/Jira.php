<?php

namespace Univerze\Jira;

class Jira
{
    /**
     * Url of the jira including https://
     * @var string 
     */
    var $url;

    /**
     * Username for theaccess
     * @var string 
     */
    var $username;

    /**
     * Password of the user
     * @var string 
     */
    var $password;

    /**
     * Search function to search issues with JQL string
     *
     * @param null $url
     * @param null $username
     * @param null $password
     * @return void
     */
    public function __construct( $url = null, $username = null, $password = null ) {
        if ( is_null($url) )
        {
            $this->url = config('jira.url');
        }
        if ( is_null($username) )
        {
            $this->username = config('jira.username');
        }
        if ( is_null($password) )
        {
            $this->password = config('jira.password');
        }
    }

    /**
     * Search function to search issues with JQL string
     *
     * @param null $jql
     * @param false $returnAll
     * @param int $startAt
     * @param int $maxResults
     * @return mixed
     */
    public function search( $jql = NULL, $returnAll = false, $startAt = 0, $maxResults = 50 )
    {
        $data   = json_encode(array('jql' => $jql, 'startAt' => $startAt, 'maxResults' => $maxResults));
        $result = self::request('search', $data);
        $result = json_decode($result);
        if( $returnAll && $result->total > $result->startAt + $result->maxResults ) {
            $nextPageIssues = $this->search($jql, true, $startAt + $maxResults, $maxResults);
            foreach ( $nextPageIssues->issues as $issue ) {
                $result->issues[] = $issue;
            }
            $result->total = count( $result->issues );
        }
        return $result;
    }

    /**
     * Create function for getting all versions of a project
     *
     * @param string $projectName
     * @return mixed
     */
    public function getVersions( $projectName )
    {
        $result = self::request('project/' . $projectName . "/versions");

        return json_decode($result);
    }

    /**
     * Create function for getting details of a version
     *
     * @param string $versionId
     * @return mixed
     */
    public function getVersion( $versionId )
    {
        $result = self::request('version/' . $versionId);

        return json_decode($result);
    }

    /**
     * Create function for getting count of related issues for a version
     *
     * @param string $versionId
     * @return mixed
     */
    public function getVersionRelatedIssueCount( $versionId )
    {
        $result = self::request('version/' . $versionId . "/relatedIssueCounts");

        return json_decode($result);
    }

    /**
     * Create function for getting count of unresolved issues for a version
     *
     * @param string $versionId
     * @return mixed
     */
    public function getVersionUnresolvedIssueCount( $versionId )
    {
        $result = self::request('version/' . $versionId . "/unresolvedIssueCount");

        return json_decode($result);
    }

    /**
     * Create function for getting all translations for an issue in context to the user
     *
     * @param string $issueId
     * @return mixed
     */
    public function getTransitions( $issueId )
    {
        $result = self::request('issue/' . $issueId . "/transitions");

        return json_decode($result);
    }

    /**
     * Create function for doing a transition on an issue
     *
     * @param string $issueId
     * @param string $transitionId
     * @return mixed
     */
    public function doTransitions( $issueId, $transitionId )
    {
        $data   = json_encode(array('transition' => ["id" => $transitionId]));
        $result = self::request('issue/' . $issueId . "/transitions", $data, 1);

        return json_decode($result);
    }

    /**
     * Update function to set a new fixVersion for an issue
     *
     * @param string $issueId
     * @param string $versionId
     * @return mixed
     */
    public function updateVersion( $issueId, $versionId )
    {
        $data = json_encode(["update" => ["fixVersions" => [["set" => [["id" => $versionId]]]]]]);
        $result = self::request('issue/' . $issueId, $data, false, true);

        return json_decode($result);
    }

    /**
     * Create function to create a single issue from array data
     *
     * @param array $data
     * @return mixed
     */
    public function create( array $data )
    {
        $data   = json_encode( array( 'fields' => $data ) );
        $result = self::request( 'issue', $data, true );

        return json_decode( $result );
    }

    /**
     * Update function to change existing issue attributes
     *
     * @param string $issue
     * @param array $data
     * @return mixed
     */
    public function update( $issue, array $data )
    {
        $data   = json_encode( array( 'fields' => $data ) );
        $result = self::request( 'issue/' . $issue, $data, false, true );

        return json_decode( $result );
    }

    /**
     * CURL request to the JIRA REST api (v2)
     *
     * @param $request
     * @param null $data
     * @param int $is_post
     * @param int $is_put
     * @return mixed
     */
    private function request( $request, $data = null, $is_post = 0, $is_put = 0 )
    {
        $ch = curl_init();

        curl_setopt_array( $ch, array(
            CURLOPT_URL            => $this->url . '/rest/api/2/' . $request,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_HTTPHEADER     => array( 'Content-type: application/json' ),
            CURLOPT_RETURNTRANSFER => 1,
        ) );
        if( !is_null($data) )
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

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