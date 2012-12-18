#!/usr/bin/env python3.2
# -*- coding: utf8 -*-

import sys
from collections import defaultdict
from fractions import Fraction
from decimal import Decimal
from functools import reduce
from math import ceil, log


def arith(s):
    print('<tr><th>п1</th><th>п2</th><th>п3</th><th>п4</th></tr>')
    total = Fraction(1)
    tau = defaultdict(lambda: 0)
    iii = 0
    for n,c in enumerate(s):
        print('<tr class="' + ('odd' if iii % 2 == 0 else 'even') + '"><td>{0}</td><td>{1}</td><td>{2}</td><td>{3} / {4}</td></tr>'.format(n, c, tau[c], 2*tau[c]+1, 2*n+256))
        iii += 1
        total *= Fraction(2*tau[c]+1, 2*n+256)
        tau[c] += 1
    print('<tr><td colspan="4"><center>Total bits: {}</center></td></tr>'.format(ceil(-log(total, 2))+1))


##############################################################


def elias(n):
    if n == 1:
        return '0'
    def binmy(n):
        return bin(n)[3:]
    def unar(n):
        return '1'*(n-1) + '0'
    return unar(len(binmy(len(binmy(n))))+2) + binmy(len(binmy(n))) + binmy(n)

def binz(n, w):
    b = bin(n)[2:]
    return '0'*(ceil(log(w,2))-len(b)) + b if w > 1 else ''

def lz77(s):
    print('<tr><th>п1</th><th>п2</th><th>п3</th><th>п4</th><th>п5</th></tr>')
    frmt = '<tr class="{}"><td>{}</td><td>{}</td><td>{}</td><td>{}</td><td>{}</td></tr>'
    window = 0
    def find_closest(i):
        for j in range(i,len(s)+1):
            f = s.rfind(s[i:j], 0, window)
            if f != -1:
                d = window-f-1
                l = j-i
            else:
                break
        return (d,l)

    i = 0
    total = 0
    iii = 0
    while i < len(s):
        d,l = find_closest(i)
        if l != 0:
            d_bin = binz(d, window)
            l_bin = elias(l)
            tl = 1 + len(d_bin) + len(l_bin)
            print(frmt.format('odd' if iii % 2 == 0 else 'even',
                              s[i:i+l],
                              l,
                              '{}({})'.format(d,window),
                              '1 ' + d_bin + ' ' + l_bin,
                              tl))
            iii += 1
            window += l
            i += l
        else:
            tl = 1 + 8
            print(frmt.format('odd' if iii % 2 == 0 else 'even',
                              s[i],
                              l,
                              '-',
                              '0 bin({})'.format(s[i]),
                              tl))
            iii += 1
            window += 1
            i += 1
        total += tl
    print('<tr><td colspan="5"><center>Total bits: {}</center></td></tr>'.format(total))


##########################################################


def lz78(s):
    print('<tr><th>п1</th><th>п2</th><th>п3</th><th>п4</th></tr>')
    frmt = '<tr class="{}"><td>{}</td><td>{}</td><td>{}</td><td>{}</td></tr>'
    dic = ['']

    i = 0
    total = 0
    k = 0
    iii = 0
    while i < len(s):
        prevk = k
        for j in range(i, len(s)+1):
            if s[i:j] not in dic:
                break
            f = dic.index(s[i:j])
            k = f
        if k == 0:
            c = binz(k, len(dic))
            neww = dic[prevk] + s[i]
            print(frmt.format('odd' if iii % 2 == 0 else 'even',
                              neww,
                              k,
                              c + ' bin({})'.format(s[i+len(dic[k])]),
                              len(c)+8))
            iii += 1
            dic.append(s[i])
            k = len(dic)-1
            i += 1
            total += len(c)+8
        else:
            c = binz(k, len(dic))
            neww = dic[prevk] + dic[k][0]
            print(frmt.format('odd' if iii % 2 == 0 else 'even',
                              neww,
                              k,
                              c,
                              len(c)))
            iii += 1
            neww = dic[prevk] + dic[k][0]
            i += len(dic[k])
            total += len(c)
        if neww not in dic:
            dic.append(neww)
    print('<tr><td colspan="4"><center>Total bits: {}</center></td></tr>'.format(total))


##########################################################################


def ppma(s, D=5):
    print('<tr><th>п1</th><th>п2</th><th>п3</th><th>п4</th><th>п5</th></tr>')
    frmt = '<tr class="{}"><td>{}</td><td>{}</td><td>{}</td><td>{}</td><td>{}</td></tr>'
    letters_left = 256

    def find_context(i):
        k = 0
        for j in range(min(i,D)+1):
            f = s.find(s[i-j:i], 0, i-1)
            if f == -1:
                break
            k = j
        return s[i-k:i]
    def context_stat(i, cont):
        stat = defaultdict(lambda: 0)
        p = 0
        while p < i:
            p = s.find(cont, p, i - 1)
            if p == -1:
                break
            stat[s[p+len(cont)]] += 1
            p += 1
        return stat
    def calc_numbers(i, context, seen=set()):
        c = s[i]
        stat = context_stat(i, context)
        for b in seen:
            del stat[b]
        sm = sum(stat.values())
        if c in stat.keys():
            return ([sm], [], Fraction(stat[c], sm+1))
        else: # need esc
            if context:
                taus, p_escs, p_a = calc_numbers(i, context[1:], set(stat.keys()) | seen)
            else:
                nonlocal letters_left
                taus, p_escs, p_a = [], [], Fraction(1, letters_left)
                letters_left -= 1
            return ([sm] + taus, [Fraction(1, sm + 1)] + p_escs, p_a)

    def fracs(fs):
        return ' * '.join(map(frac, fs))
    def frac(f):
        if f.denominator == 1:
            return str(f.numerator)
        else:
            return '{} / {}'.format(f.numerator, f.denominator)

    i = 0
    total = Decimal(0)
    while i < len(s):
        letter = s[i]
        context = find_context(i)
        taus, p_escs, p_a = calc_numbers(i, context)
        if not context:
            context = r'$\varnothing$'
        elif isinstance(context, bytes):
            context = ' '.join(map(hex, context))
        fr1 = fracs(p_escs)
        if fr1:
            fr1 = '$'+fr1+'$'
        fr2 = frac(p_a)
        if fr2:
            fr2 = '$'+fr2+'$'
        print(frmt.format('odd' if i % 2 == 0 else 'even',
                          hex(letter),
                          context,
                          ';'.join(map(str,taus)),
                          fr1,
                          fr2))
        i += 1
        total += sum(map(lambda p: Decimal.from_float(-log(p, 2)), p_escs + [p_a]))
    print('<tr><td colspan="5"><center>Total bits: {}</center></td></tr>'.format(ceil(total)+1))


##########################################################################


if __name__ == "__main__":
    assert(len(sys.argv) == 2)

    algo = sys.argv[1]
    with open('input.txt', 'r') as f:
        s = f.read()

    print('<table id="infoTable">')

    if algo == "arith":
        arith(s)
    elif algo == "lz77":
        lz77(s)
    elif algo == "lz78":
        lz78(s)
    elif algo == "ppma":
        ppma(s)

    print('</table>')
