#!/usr/bin/env python3.2

from collections import defaultdict
from fractions import Fraction
from decimal import Decimal
from functools import reduce
from math import ceil, log


def arith(s):
    total = Fraction(1)
    tau = defaultdict(lambda: 0)
    for n,c in enumerate(s):
        print('{0:<5}{1:<7}{2:<16}\sfrac{{{3}}}{{{4}}}'.format(n, c, tau[c], 2*tau[c]+1, 2*n+256))
        total *= Fraction(2*tau[c]+1, 2*n+256)
        tau[c] += 1
    print('Total bits: {}'.format(ceil(-log(total, 2))+1))


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
    frmt = '{:<25}{:<5}{:<9}{:<20}{}'
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
    while i < len(s):
        d,l = find_closest(i)
        if l != 0:
            d_bin = binz(d, window)
            l_bin = elias(l)
            tl = 1 + len(d_bin) + len(l_bin)
            print(frmt.format(s[i:i+l],
                              l,
                              '{}({})'.format(d,window),
                              '1 ' + d_bin + ' ' + l_bin,
                              tl))
            window += l
            i += l
        else:
            tl = 1 + 8
            print(frmt.format(s[i],
                              l,
                              '-',
                              '0 bin({})'.format(s[i]),
                              tl))
            window += 1
            i += 1
        total += tl
    print('Total bits: {}'.format(total))


##########################################################


def lz78(s):
    frmt = '{:<15}{:<15}{:<20}{}'
    dic = ['']

    i = 0
    total = 0
    k = 0
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
            print(frmt.format(neww,
                              k,
                              c + ' bin({})'.format(s[i+len(dic[k])]),
                              len(c)+8))
            dic.append(s[i])
            k = len(dic)-1
            i += 1
            total += len(c)+8
        else:
            c = binz(k, len(dic))
            neww = dic[prevk] + dic[k][0]
            print(frmt.format(neww,
                              k,
                              c,
                              len(c)))
            neww = dic[prevk] + dic[k][0]
            i += len(dic[k])
            total += len(c)
        if neww not in dic:
            dic.append(neww)
    print('Total bits: {}'.format(total))


##########################################################################


def ppma(s, D=5):
    frmt = '{:<10}{:<30}{:<20}{:<100}{}'
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
        return '\cdot'.join(map(frac, fs))
    def frac(f):
        if f.denominator == 1:
            return str(f.numerator)
        else:
            return '\sfrac{{{}}}{{{}}}'.format(f.numerator, f.denominator)

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
        print(frmt.format(hex(letter),
                          context,
                          ';'.join(map(str,taus)),
                          fr1,
                          fr2))
        i += 1
        total += sum(map(lambda p: Decimal.from_float(-log(p, 2)), p_escs + [p_a]))
    print('Total bits: {}'.format(ceil(total)+1))
