
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.Map;

public class LZW {

	private String s;
	
	public LZW(File file) throws IOException {
		BufferedReader bf = null;
		try {
			bf = new BufferedReader(new InputStreamReader(new FileInputStream(file), "utf-8"));
			this.s = bf.readLine();
		} finally {
			if (bf != null) {
				bf.close();
			}
		}
	}
	
	private static String makeRow(int step, String word, int number, String code, int length) {
		StringBuilder sb = new StringBuilder();
		sb.append(String.format("<tr class=\"%s\">", (step & 1) == 0 ? "even" : "odd"));
		sb.append(String.format("<td>%d</td>", step));
		sb.append(String.format("<td>%s</td>", word));
		sb.append(String.format("<td>%d</td>", number));
		sb.append(String.format("<td>%s</td>", code));
		sb.append(String.format("<td>%d</td>", length));
		sb.append("</tr>");
		return sb.toString();
	}
	
	public void code(File file) throws IOException {
		PrintWriter out = null;
		try {
			out = new PrintWriter(new OutputStreamWriter(new FileOutputStream(file), "utf-8"));
			out.println("<table id=\"infoTable\">");
			out.println("<tr><th>Шаг</th><th>Добавленное слово</th><th>Номер слова</th><th>Код</th><th>Длина кода</th></tr>");
			
			Map<String, Integer> map = new HashMap<String, Integer>();
			String p = "";
			int bits = 0;
			int step = 0;
			boolean isCode = true;
			for (int i = 0; i < s.length(); ++i) {
				p += s.charAt(i);
				if (map.containsKey(p)) {
					isCode = false;
					continue;
				}
				String tmp = p.substring(0, p.length() - 1);
				if (tmp.isEmpty()) {
					++step;
					System.out.println("String \'" + p + "\'; " + toBinaryString(0, length(map.size())) + "bin(" + s.charAt(i) + ")");
					
					out.println(makeRow(step, p, 0, toBinaryString(0, length(map.size())) + "bin(" + s.charAt(i) + ")", length(map.size()) + 8));
					bits += length(map.size()) + 8;
					map.put(new String(p), map.size() + 1);
					p = "";
					isCode = true;
				} else {
					++step;
					int shift = map.get(tmp);
					//int length = length(map.size());
					System.out.println("String \'" + p + "\'; " + toBinaryString(shift, length(map.size())));
					
					out.println(makeRow(step, p, shift, toBinaryString(shift, length(map.size())), length(map.size())));
					bits += length(map.size());
					map.put(new String(p), map.size() + 1);
					p = p.substring(tmp.length());
					isCode = true;
					if (!map.containsKey(p)) {
						++step;
						System.out.println("String \'" + p + "\'; " + toBinaryString(0, length(map.size())) + "bin(" + s.charAt(i) + ")");
						
						out.println(makeRow(step, p, 0, toBinaryString(0, length(map.size())) + "bin(" + s.charAt(i) + ")", length(map.size()) + 8));
						bits += length(map.size()) + 8;
						map.put(new String(p), map.size() + 1);
						p = "";
					}
				}
			}
			if (!isCode) {
				++step;
				int shift = map.get(p);
				System.out.println("String \'-\'; " + toBinaryString(shift, length(map.size())));
				
				out.println(makeRow(step, "-", shift, toBinaryString(shift, length(map.size())), length(map.size())));
				bits += length(map.size());
			}
			System.out.println(bits + " bits");
			out.println("</table>");
			out.println("<p><b>Длина сообщения: " + bits + "</b></p>");
		} finally {
			if (out != null) {
				out.close();
				if (out.checkError()) {
					throw new IOException("I/O error");
				}
			}
		}
	}
	
	private static String toBinaryString(int n, int length) {
		StringBuilder sb = new StringBuilder();
		while (--length >= 0) {
			sb.append((char)'0' + n & 1);
			n >>= 1;
		}
		return sb.reverse().toString();
	}
	
	private static int length(int n) {
		return n == 0 ? 0 : 1 + (int) Math.floor(Math.log(n) / Math.log(2));
	}
}
