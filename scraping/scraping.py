# /*
#  * Copyright 2024 留年プロテクタープロジェクト
#  * This file is part of RPRO.
#  * 
#  * RPRO is free software: you can redistribute it and/or modify it under the terms of
#  * the GNU General Public License as published by the Free Software Foundation, either 
#  * version 3 of the License, or (at your option) any later version.
#  * 
#  * RPRO is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
#  * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
#  * PURPOSE. See the GNU General Public License for more details.
#  * You should have received a copy of the GNU General Public License along with RPRO.
#  * If not, see <https://www.gnu.org/licenses/>.
#  */

# /*
#  * scraping.py
#  * 
#  * scraping.py is file for scraping class datas.
#  */


import requests
from bs4 import BeautifulSoup
import time
import re
# 6401~6438は4年生の科目全てを示す
for i in range(6401, 6438):
    # リクエストURL
    req = requests.get(
        "https://syllabus.kosen-k.go.jp/Pages/PublicSyllabus?school_id=27&department_id=14&subject_code=%d&year=2021&lang=ja" % (i))
    req.encoding = req.apparent_encoding

    bsObj = BeautifulSoup(req.text, "html.parser")

    # 一致検索・正規表現による検索
    items = bsObj.find("table", id="MainContent_SubjectSyllabus_wariaiTable")
    name = bsObj.find("h1").get_text()
    kekka = bsObj.find_all(string=re.compile("欠席条件.*"))
    kaisuu = bsObj.find_all('td')[14]
    mul = re.sub(r'\D', "", str(kaisuu))
#    print(kaisuu)
#    print(mul)
    kazu = 16*int(mul)/2
#    print(kazu)
    # ファイルへの書き込み
    f = open("class_id%d" % (i), 'w')
    f.write("科目ID：%s" % (str(i)))
    f.write("\n")
    f.write(str(name))
    f.write("\n")
    f.write("授業回数：%s" % (str(kazu)))
    f.write("\n")
    f.write(str(kekka))
    f.write("\n")
    for item in items:
        f.write(str(item))
    f.close()
    print("class_id=%d" % (i))
    # サーバの負荷軽減の為のスリープ
    time.sleep(5)
    print("next page...")
