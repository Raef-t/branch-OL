import 'package:flutter/material.dart';
import '/core/components/divider_with_fixed_size_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/sized_boxs/widths.dart';
import '/features/work_hours_to_all_students/presentation/view/widgets/custom_image_and_text_in_work_hours_view.dart';
import '/gen/assets.gen.dart';

class CustomTabBarsInWorkHoursView extends StatelessWidget {
  const CustomTabBarsInWorkHoursView({
    super.key,
    required this.selectedIndex,
    required this.applyOnTap,
    required this.menuOnTap,
  });
  final int selectedIndex;
  final void Function() applyOnTap, menuOnTap;
  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.right30(
      context: context,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.end,
        children: [
          GestureDetector(
            onTap: applyOnTap,
            child: Column(
              children: [
                CustomImageAndTextInWorkHoursView(
                  image: Assets.images.applyImage.image(),
                  text: 'تطبيق',
                ),
                selectedIndex == 1
                    ? const DividerWithFixedSizeComponent()
                    : const SizedBox.shrink(),
              ],
            ),
          ),
          Widths.width33(context: context),
          GestureDetector(
            onTap: menuOnTap,
            child: Column(
              children: [
                CustomImageAndTextInWorkHoursView(
                  image: Assets.images.horizontalMenuImage.image(),
                  text: 'قائمة',
                ),
                selectedIndex == 0
                    ? const DividerWithFixedSizeComponent()
                    : const SizedBox.shrink(),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
