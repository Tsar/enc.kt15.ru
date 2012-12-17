// NOT ORIGINAL! html table mod.

import java.io.*;
import java.util.*;

public class PPMA_Oleg_Mamin {
	
	private static final int D = 5;
	
	private Scanner s;
	private PrintWriter pw;
	
	PPMA_Oleg_Mamin(String fileName) {
		try {
			s = new Scanner(new BufferedInputStream(new FileInputStream(new File(fileName))), "UTF-8");
			pw = new PrintWriter(new File("output.txt"), "UTF-8");
		} catch (IOException e) {
			System.err.println("Error on initializing PPMA");
			e.printStackTrace();
		}
	}
	
	private ArrayList<Integer> count(String s, String pattern, HashSet<Character> badChars) {
		ArrayList<Integer> pos = new ArrayList<Integer>();
		if (pattern.length() == 0) {
			for (int i = 0, l = s.length(); i < l; ++i) {
				if (!badChars.contains(s.charAt(i))) {
					pos.add(i);
				}
			}
			return pos;
		}
		for (int i = 0, l = s.length(), p = pattern.length(); i < l - 2 * p + 1; ++i) {
			if (s.substring(i, i + p).equals(pattern) && !badChars.contains(s.charAt(i + p))) {
				pos.add(i);
			}
		}
		return pos;
	}
	
	private int countAfter(String s, char c, ArrayList<Integer> pos, int d) {
		int ans = 0;
		int l = s.length();
		for (Integer e : pos) {
			if (e + d < l && s.charAt(e + d) == c) {
				++ans;
			}
		}
		return ans;
	}
	
	/*private int countMt(String s, ArrayList<Integer> pos, int d) {
		HashSet<Character> set = new HashSet<Character>();
		int l = s.length();
		for (Integer e : pos) {
			if (e + d < l) {
				set.add(s.charAt(e + d));
			}
		}
		return set.size();
	}*/
	
	public void encode() {
		HashSet<Character> used = new HashSet<Character>();
		String str = s.nextLine();
		double size = 0;

		pw.println("<table id=\"infoTable\">");
		pw.println("<tr><th>Step</th><th>Char</th><th>Context <i>s</i></th><th>tau_t'(<i>s</i>)</th><th><i>p_t</i>(<i>esc</i>|<i>s</i>)</th><th><i>p_t</i>(<i>a</i>|<i>s</i>)</th></tr>");

		for (int i = 0, l = str.length(); i < l; ++i) {
			char cur = str.charAt(i);
			String context = i == 0 ? "" : str.substring(Math.max(0, i - D), i);
			int d = context.length();
			HashSet<Character> badChars = new HashSet<Character>();
			ArrayList<Integer> pos;
			while ((pos = count(i == 0 ? "" : str.substring(0, i), context, badChars)).size() == 0 && d >= 0) {
				--d;
				context = i == 0 ? "" : str.substring(Math.max(0, i - d), i);
			}

			//System.out.print("Step: " + (i + 1) + "; Char: " + cur + "; Context: " + (context.length() == 0 ? "#" : context) + "; t_t'(s): " + pos.size());///////////
			//pw.print("Step: " + (i + 1) + "; Char: " + cur + "; Context: " + (context.length() == 0 ? "#" : context) + "; t_t'(s): " + pos.size());///////////
			pw.print("<tr class=\"" + (i % 2 == 0 ? "odd" : "even") + "\"><td>" + (i + 1) + "</td><td>" + cur + "</td><td>" + (context.length() == 0 ? "#" : context) + "</td><td>" + pos.size());

			int cnt;
			double pEsc = 1.0 / (pos.size() + 1);
			boolean needEsc = false;
			String pEscStr = "1/" + (pos.size() + 1);
			while ((cnt = countAfter((i == 0 ? "" : str.substring(0, i)), cur, pos, d)) == 0 && d >= 0) {
				for (Integer e : pos) {
					if (e + d < i) {
						badChars.add(str.charAt(e + d));
					}
				}
				needEsc = true;
				--d;
				if (d < 0) {
					break;
				}
				context = i == 0 ? "" : str.substring(Math.max(0, i - d), i);
				pos = count(i == 0 ? "" : str.substring(0, i), context, badChars);

				//System.out.print(", " + pos.size());/////////////
				//pw.print(", " + pos.size());/////////////
				pw.print(", " + pos.size());

				if (countAfter((i == 0 ? "" : str.substring(0, i)), cur, pos, d) == 0) {
					pEsc *= 1.0 / (pos.size() + 1);
					pEscStr += " * 1/" + (pos.size() + 1);
				}
			}

			//System.out.print("; p(esc | s) = " + (needEsc || i == 0 ? pEscStr : "") + "; p(a | s) = " + (cnt == 0 ? "1/" + (256 - used.size()) : cnt + "/" + (pos.size() + 1)));
			//System.out.println();
			//pw.print("; p(esc | s) = " + (needEsc || i == 0 ? pEscStr : "") + "; p(a | s) = " + (cnt == 0 ? "1/" + (256 - used.size()) : cnt + "/" + (pos.size() + 1)));
			//pw.println();
			pw.println("</td><td>" + (needEsc || i == 0 ? pEscStr : "") + "</td><td>" + (cnt == 0 ? "1/" + (256 - used.size()) : cnt + "/" + (pos.size() + 1)) + "</td></tr>");

			used.add(cur);
			double tmp = needEsc || i == 0 ? pEsc : 1;
			tmp *= cnt == 0 ? 1.0 / (256 - used.size()) : (double)(cnt) / (pos.size() + 1);
			size -= Math.log(tmp) / Math.log(2.0);
		}

		//System.out.println("Size: " + (Math.ceil(size) + 1));
		//pw.println("Size: " + (Math.ceil(size) + 1));
		pw.println("</table>");
		pw.println("<p><b>Size: " + (Math.ceil(size) + 1) + "</b></p>");

		pw.close();
	}

}
