<?php
/**
 * Frédéric - Projet 3 - Formation OpenClassrooms - 14/06/17 19:11
 */

namespace Blog\DAO;

use Blog\Domain\Article;

class ArticleDAO extends DAO
{
    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from article order by id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }
        return $articles;
    }

    /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id
     * @return \Blog\Domain\Article
     * @throws \Exception
     */
    public function find($id) {
        $sql = "select * from article where id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching id " . $id);
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \Blog\Domain\Article
     */
    protected function buildDomainObject(array $row) {
        $article = new Article();
        $article->setId($row['id']);
        $article->setDate($row['date']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);
        return $article;
    }

    /**
     * Saves an article into the database.
     *
     * @param \Blog\Domain\Article $article The article to save
     */
    public function save(Article $article) {
        $articleData = array(
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
        );

        if ($article->getId()) {
            // The article has already been saved : update it
            $this->getDb()->update('article', $articleData, array('id' => $article->getId()));
        } else {
            // The article has never been saved : insert it
            $this->getDb()->insert('article', $articleData);
            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    /**
     * Removes an article from the database.
     *
     * @param integer $id The article id.
     */
    public function delete($id) {
        // Delete the article
        $this->getDb()->delete('article', array('id' => $id));
    }


}