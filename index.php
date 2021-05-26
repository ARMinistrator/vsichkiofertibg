<?php

class Item {

}

interface FeedSearchInterface {
    public function brand(string $category): self;

    public function used(): self;

    public function new(): self;

    public function cheapest(): Item;

    public function mostExpensive(): Item;
}

class FeedSearch implements FeedSearchInterface {
    protected $feed;
    protected $items;

    public function __construct($feed) {
        $this->feed = $feed;
        $this->items = [];
    }

    public function brand(string $category): FeedSearchInterface {
        $newsXml = simplexml_load_file($this->feed);
        $namespaces = $newsXml->getNamespaces(true);
        foreach ($newsXml->channel->item as $item) {
            $media = (array)$item->children($namespaces["g"]);
            if ($media['brand'] == $category) {
                $this->items[$media['id']]['id'] = $media['id'];
                $this->items[$media['id']]['availability'] = $media['availability'];
                $this->items[$media['id']]['price'] = $media['price'];
                $this->items[$media['id']]['brand'] = $media['brand'];
                $this->items[$media['id']]['condition'] = $media['condition'];
            }
        }
        return $this;
    }

    public function used(): FeedSearchInterface {
        foreach ($this->items as $item) {
            if ($item['condition'] != 'used') {
                unset($this->items[$item['id']]);
            }
        }
        return $this;
    }

    public function new(): FeedSearchInterface {
        foreach ($this->items as $item) {
            if ($item['condition'] != 'new') {
                unset($this->items[$item['id']]);
            }
        }
        return $this;
    }

    public function cheapest(): Item {
        return new Item;
    }

    public function mostExpensive(): Item {
        return new Item;
    }
}

$feed = 'feed.xml';
$fs = new FeedSearch($feed);
$item = $fs->brand('SONY')
    ->used()
    ->cheapest();
echo $item->id;
