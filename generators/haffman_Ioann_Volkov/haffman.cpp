#include <iostream>
#include <queue>
#include <string>
#include <map>

struct V {
    double p;
    char c;
    V* s1;
    V* s2;

    V()
        : s1(0), s2(0) {
    }

    V(double p, char c)
        : p(p), c(c), s1(0), s2(0) {
    }

    V(double p, char c, V* s1, V* s2)
        : p(p), c(c), s1(s1), s2(s2) {
    }
};

bool operator<(V const& v1, V const& v2) {
    return v1.p > v2.p;
}

void buildCodes(V const* root, std::map<char, std::string>& res, std::string codePrefix = "") {
    if (root->s1) {
        buildCodes(root->s1, res, codePrefix + "0");
        buildCodes(root->s2, res, codePrefix + "1");
    } else {
        res[root->c] = codePrefix;
    }
}

int main() {
    FILE* f1 = fopen("input.txt", "rb");
    fseek(f1, 0, SEEK_END);
    long f1Size = ftell(f1);
    fseek(f1, 0, SEEK_SET);
    char* b1 = new char[f1Size];
    fread(b1, 1, f1Size, f1);
    std::string s(b1, f1Size);
    delete[] b1;
    fclose(f1);

    //std::string s = "Better_the_devil_you_know_than_the_devil_you_don't_know";
    std::map<char, int> count;
    for (int i = 0; i < s.length(); ++i) {
        if (count.find(s[i]) != count.end()) {
            ++count[s[i]];
        } else {
            count[s[i]] = 1;
        }
    }

    std::priority_queue<V> v;
    for (std::map<char, int>::const_iterator it = count.begin(); it != count.end(); ++it) {
        v.push(V(((double)it->second) / s.length(), it->first));
    }

    while (v.size() > 1) {
        V* s1 = new V(v.top());
        v.pop();
        V* s2 = new V(v.top());
        v.pop();
        v.push(V(s1->p + s2->p, 0, s1, s2));
    }
    
    std::cout << "<table id=\"infoTable\">" << std::endl << "<tr><th>Char</th><th>Code</th><th>Current length</th></tr>" << std::endl;

    std::map<char, std::string> codes;
    buildCodes(&v.top(), codes);
    std::string completeCode = "";
    for (int i = 0; i < s.length(); ++i) {
        completeCode += codes[s[i]];
        std::cout << "<tr class=\"" << (i % 2 == 0 ? "odd" : "even") << "\"><td>" << s[i] << "</td><td>" << codes[s[i]] << "</td><td>" << completeCode.length() << "</td></tr>" << std::endl;
    }
    std::cout << "</table>" << std::endl;
    
    std::cout << "<p><b>Bits for phrase code: " << completeCode.length() << "<br />" << std::endl;
    std::cout << "Bits for chars:       " << count.size() * 8 << "<br />" << std::endl;
    std::cout << "Bits for tree:        " << 2 * count.size() - 1 << "<br /></b></p>" << std::endl;

    std::cout << "<p><b>Sum = " << completeCode.length() + count.size() * 8 + 2 * count.size() - 1 << "</b></p>" << std::endl;

    return 0;
}
