<?php
class Musikarkiv_Item {
    private $id;
    private $added;
    private $updated;
    private $sortedArtist;
    private $title;
    private $artist;
    private $artistID;
    private $type;
    private $discogsType;
    private $description;
    private $donated;
    private $link;
    private $image;
    private $thumbnail;
    private $collection;
    private $public;
    private $archived;
    private $discogsID;
    private $discogsMaster;
    private $releaseYear;

    public function __construct($data) {
        $this->id = $data->id;
        $this->added = $data->added;
        $this->updated = $data->updated;
        $this->sortedArtist = $data->sortedArtist;
        $this->title = $data->title;
        $this->artist = $data->artist;
        $this->artistID = $data->artistID;
        $this->type = $data->type;
        $this->discogsType = $data->discogsType;
        $this->description = $data->description;
        $this->donated = $data->donated;
        $this->link = $data->link;
        $this->image = $data->image;
        $this->thumbnail = $data->thumbnail;
        $this->collection = $data->collection;
        $this->public = $data->public;
        $this->archived = $data->archived;
        $this->discogsID = $data->discogsID;
        $this->discogsMaster = $data->discogsMaster;
        $this->releaseYear = isset($data->releaseYear) ? $data->releaseYear : null;
    }

    public function getId() { return $this->id; }
    public function getAdded() { return $this->added; }
    public function getUpdated() { return $this->updated; }
    public function getSortedArtist() { return $this->sortedArtist; }
    public function getTitle() { return $this->title; }
    public function getArtist() { return $this->artist; }
    public function getArtistID() { return $this->artistID; }
    public function getType() { return $this->type; }
    public function getDiscogsType() { return $this->discogsType; }
    public function getDescription() { return $this->description; }
    public function getDonated() { return $this->donated; }
    public function getLink() { return $this->link; }
    public function getImage() { return $this->image; }
    public function getThumbnail() { return $this->thumbnail; }
    public function getCollection() { return $this->collection; }
    public function getPublic() { return $this->public; }
    public function getArchived() { return $this->archived; }
    public function getDiscogsID() { return $this->discogsID; }
    public function getDiscogsMaster() { return $this->discogsMaster; }
    public function getReleaseYear() { return $this->releaseYear; }

    public function setId($id) { $this->id = $id; }
    public function setAdded($added) { $this->added = $added; }
    public function setUpdated($updated) { $this->updated = $updated; }
    public function setSortedArtist($sortedArtist) { $this->sortedArtist = $sortedArtist; }
    public function setTitle($title) { $this->title = $title; }
    public function setArtist($artist) { $this->artist = $artist; }
    public function setArtistID($artistID) { $this->artistID = $artistID; }
    public function setType($type) { $this->type = $type; }
    public function setDiscogsType($discogsType) { $this->discogsType = $discogsType; }
    public function setDescription($description) { $this->description = $description; }
    public function setDonated($donated) { $this->donated = $donated; }
    public function setLink($link) { $this->link = $link; }
    public function setImage($image) { $this->image = $image; }
    public function setThumbnail($thumbnail) { $this->thumbnail = $thumbnail; }
    public function setCollection($collection) { $this->collection = $collection; }
    public function setPublic($public) { $this->public = $public; }
    public function setArchived($archived) { $this->archived = $archived; }
    public function setDiscogsID($discogsID) { $this->discogsID = $discogsID; }
    public function setDiscogsMaster($discogsMaster) { $this->discogsMaster = $discogsMaster; }
    public function setReleaseYear($releaseYear) { $this->releaseYear = $releaseYear; }

    public function __toString() {
        return $this->artist . ': ' . $this->title;
    }
}
