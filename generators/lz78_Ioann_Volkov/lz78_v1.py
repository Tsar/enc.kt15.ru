from math import log, ceil

log2 = lambda x: log(x) / log(2.0)

def myBin(x, W):
    res = bin(x)[2:]
    while len(res) < ceil(log2(W)):
        res = "0" + res
    return res

def myBin2(x, W):
    res = bin(x)[2:]
    while len(res) < 8 + ceil(log2(W)):
        res = "0" + res
    return res

if __name__ == "__main__":
    with open("input.txt", "r") as f:
        s = f.read()

    n = len(s)
    N = 0
    
    dic = []

    codeLen = 0
    
    print "<table><tr><th>Символы</th><th>Номер слова в словаре</th><th>Код</th>Длина всего кода</tr>"

    while N < n:
        l_max = 0
        j_for_l_max = -1
        for l in xrange(1, n):
            if N + l > len(s):
                continue
            if l == 1:
                if l_max < 1:
                    l_max = 1
                    j_for_l_max = ord(s[N])
                continue
            for j in xrange(len(dic)):
                if s[N:N+l] == dic[j]:
                    if l_max < len(dic[j]):
                        l_max = len(dic[j])
                        j_for_l_max = j
        if l_max == 1:
            q = len(dic) - 1
            if q <= 0:
                q = 1
            c = myBin2(j_for_l_max, q)
            j_for_l_max = "-"
        else:
            c = myBin(j_for_l_max, len(dic) - 1)
            j_for_l_max += 1
        codeLen += len(c)
        print "<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td></tr>" % (s[N:N+l_max], j_for_l_max, c, codeLen)
        if N + l_max + 1 <= len(s):
            dic.append(s[N:N+l_max+1])
        N += l_max

    print "</table>"
