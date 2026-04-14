import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/components/text_medium12_component.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomTrailingListTileInClassView extends StatelessWidget {
  const CustomTrailingListTileInClassView({
    super.key,
    required this.batchStudentsModel,
    required this.selectedIndex,
  });
  final BatchStudentsModel? batchStudentsModel;
  final int selectedIndex;
  @override
  Widget build(BuildContext context) {
    return selectedIndex == 2
        ? Column(
            mainAxisAlignment:
                MainAxisAlignment.start, // 👈 Changed from center to start
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 15), // 👈 Added significant top padding
              TextMedium12Component(
                text:
                    r'$'
                    '${batchStudentsModel?.remainingAmount ?? 0}',
                fontFamily: FontFamily.tajawal,
                color: ColorsStyle.blackColor,
              ),
              const TextMedium12Component(
                text: 'المتبقي',
                fontFamily: FontFamily.tajawal,
                color: ColorsStyle.greyColor,
              ),
            ],
          )
        : selectedIndex == 1
        ? SvgImageComponent(
            pathImage: Assets.images.manyCircleAvatarsImage,
            color: (batchStudentsModel?.attendance ?? false)
                ? ColorsStyle.greenColor2
                : ColorsStyle.redColor,
          )
        : const SizedBox();
  }
}
