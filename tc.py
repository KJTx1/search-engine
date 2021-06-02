import pyterrier as pt
if not pt.started():
  pt.init()

files = pt.io.find_files("./www.reddit.com")
indexer = pt.FilesIndexer("./reddit_index", meta={"docno":20,"filename": 1024,"title":1024}, meta_tags={"title":"title"})
indexref = indexer.index(files)
index = pt.IndexFactory.of(indexref)
print(index.getCollectionStatistics().toString())