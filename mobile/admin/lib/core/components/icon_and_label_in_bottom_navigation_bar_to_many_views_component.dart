import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/fonts.gen.dart';

class IconAndLabelInBottomNavigationBarToManyViewsComponent
    extends StatelessWidget {
  const IconAndLabelInBottomNavigationBarToManyViewsComponent({
    super.key,
    required this.imagePath,
    required this.text,
    required this.index,
    required this.currentIndex,
    required this.onTap,
  });

  final String imagePath;
  final String text;
  final int index;
  final int currentIndex;
  final void Function(int) onTap;

  @override
  Widget build(BuildContext context) {
    final bool isSelected = index == currentIndex;

    return InkWell(
      borderRadius: BorderRadius.circular(18),
      onTap: () => onTap(index),
      child: SizedBox(
        height: double.infinity,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            SvgImageComponent(
              pathImage: imagePath,
              width: 24,
              height: 24,
              color: isSelected
                  ? ColorsStyle.deepPinkColor2
                  : const Color(0xFF222222),
            ),
            const SizedBox(height: 6),
            Text(
              text,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                fontFamily: FontFamily.tajawal,
                fontSize: 12,
                fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
                color: isSelected
                    ? ColorsStyle.deepPinkColor2
                    : const Color(0xFF222222),
                height: 1.0,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
